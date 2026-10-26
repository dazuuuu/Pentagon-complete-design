<?php

namespace App\Helpers;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Minimal allowlist-based HTML sanitizer for admin-authored rich content
 * (e.g. blog post body from TinyMCE). Strips scripts, on* attributes,
 * javascript: URLs, and any tag not on the allowlist, while keeping the
 * markup TinyMCE produces (headings, lists, tables, iframes for embeds, etc).
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'hr', 'div', 'span',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'blockquote', 'pre', 'code',
        'ul', 'ol', 'li',
        'a', 'img',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'iframe', 'figure', 'figcaption',
        'details', 'summary',
    ];

    private const ALLOWED_ATTRS = [
        'a' => ['href', 'target', 'rel', 'title', 'class'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'class', 'style'],
        'iframe' => ['src', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen', 'class', 'title'],
        'div' => ['class', 'data-url', 'style'],
        'span' => ['class', 'style'],
        'p' => ['class', 'style'],
        'table' => ['class', 'style', 'border', 'cellpadding', 'cellspacing'],
        'td' => ['class', 'style', 'colspan', 'rowspan'],
        'th' => ['class', 'style', 'colspan', 'rowspan'],
        'details' => ['class', 'open'],
        'summary' => ['class'],
        '*' => ['class', 'style'],
    ];

    private const ALLOWED_IFRAME_HOSTS = [
        'www.youtube.com', 'youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com',
        'player.vimeo.com',
    ];

    public static function clean(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="utf-8" ?><div id="pq-root">' . $html . '</div>',
            LIBXML_NOWARNING | LIBXML_NOERROR
        );
        libxml_clear_errors();

        $root = $doc->getElementById('pq-root');
        if (!$root) {
            return '';
        }

        self::cleanNode($doc, $root);

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $doc->saveHTML($child);
        }
        return trim($out);
    }

    private static function cleanNode(DOMDocument $doc, DOMNode $node): void
    {
        $children = iterator_to_array($node->childNodes);
        foreach ($children as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if ($tag === 'script' || $tag === 'style' || $tag === 'noscript' || $tag === 'object' || $tag === 'embed' || $tag === 'form') {
                    $node->removeChild($child);
                    continue;
                }

                if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                    // Unwrap: keep children, drop the tag itself.
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                if ($tag === 'iframe') {
                    $src = $child->getAttribute('src');
                    $host = parse_url($src, PHP_URL_HOST) ?: '';
                    if (!in_array(strtolower($host), self::ALLOWED_IFRAME_HOSTS, true)) {
                        $node->removeChild($child);
                        continue;
                    }
                }

                self::cleanAttributes($child, $tag);
                self::cleanNode($doc, $child);
            } elseif ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
            }
        }
    }

    private static function cleanAttributes(DOMElement $el, string $tag): void
    {
        $allowed = array_merge(self::ALLOWED_ATTRS['*'] ?? [], self::ALLOWED_ATTRS[$tag] ?? []);
        $attrs = iterator_to_array($el->attributes ?? []);
        foreach ($attrs as $attr) {
            $name = strtolower($attr->name);
            if (str_starts_with($name, 'on') || !in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);
                continue;
            }
            if (in_array($name, ['href', 'src'], true)) {
                $value = trim($attr->value);
                if (preg_match('/^\s*javascript:/i', $value) || preg_match('/^\s*data:text\/html/i', $value)) {
                    $el->removeAttribute($attr->name);
                }
            }
        }

        if ($tag === 'a') {
            $el->setAttribute('rel', 'noopener noreferrer');
        }
    }
}
