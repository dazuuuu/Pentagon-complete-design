<?php

namespace App\Services;

use App\Models\LinkPreview;
use RuntimeException;

/**
 * Fetches Open Graph metadata for a URL (for the blog editor's "Insert Link Card"
 * feature) and caches it in link_previews so repeated inserts of the same URL
 * don't re-fetch every time. Includes SSRF guards since the fetch is triggered
 * by an admin-supplied URL.
 */
class LinkPreviewService
{
    private const MAX_BYTES = 512 * 1024; // 512KB cap
    private const TIMEOUT = 5; // seconds
    private const CACHE_TTL = 86400 * 7; // 7 days
    private const USER_AGENT = 'Mozilla/5.0 (compatible; PentagonSafarisBot/1.0; +https://pentagonsafaris.com)';

    private LinkPreview $model;

    public function __construct()
    {
        $this->model = new LinkPreview();
    }

    /**
     * @return array{url:string,title:?string,description:?string,image_url:?string}
     */
    public function fetch(string $url): array
    {
        $url = trim($url);
        $this->assertSafeUrl($url);

        $cached = $this->model->findByUrl($url);
        if ($cached && !empty($cached['fetched_at']) && (time() - strtotime($cached['fetched_at'])) < self::CACHE_TTL) {
            return $cached;
        }

        $html = $this->download($url);
        [$title, $description, $image] = $this->parseMeta($html, $url);

        return $this->model->upsert($url, $title, $description, $image);
    }

    private function assertSafeUrl(string $url): void
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            throw new RuntimeException('Please enter a valid URL.');
        }

        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new RuntimeException('Only http and https URLs are allowed.');
        }

        $host = $parts['host'];

        // Resolve host to IP(s) and reject private/loopback/link-local ranges.
        $ips = [];
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips[] = $host;
        } else {
            $recordsA = @dns_get_record($host, DNS_A) ?: [];
            $recordsAAAA = @dns_get_record($host, DNS_AAAA) ?: [];
            foreach (array_merge($recordsA, $recordsAAAA) as $rec) {
                if (!empty($rec['ip'])) {
                    $ips[] = $rec['ip'];
                }
                if (!empty($rec['ipv6'])) {
                    $ips[] = $rec['ipv6'];
                }
            }
            if ($ips === []) {
                $resolved = gethostbyname($host);
                if ($resolved !== $host) {
                    $ips[] = $resolved;
                }
            }
        }

        if ($ips === []) {
            throw new RuntimeException('Could not resolve that host.');
        }

        foreach ($ips as $ip) {
            if ($this->isPrivateOrLocal($ip)) {
                throw new RuntimeException('That URL points to a private or internal address and cannot be fetched.');
            }
        }
    }

    private function isPrivateOrLocal(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }
        // Explicit extra ranges filter_var may not catch on all PHP builds.
        $blocked = [
            '127.0.0.0/8', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16',
            '169.254.0.0/16', '0.0.0.0/8',
        ];
        foreach ($blocked as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }
        if ($ip === '::1' || str_starts_with($ip, 'fc') || str_starts_with($ip, 'fd') || str_starts_with($ip, 'fe80')) {
            return true;
        }
        return false;
    }

    private function ipInRange(string $ip, string $range): bool
    {
        if (!str_contains($ip, '.')) {
            return false;
        }
        [$subnet, $bits] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false) {
            return false;
        }
        $mask = -1 << (32 - (int) $bits);
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    private function download(string $url): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => self::TIMEOUT,
                'header' => "User-Agent: " . self::USER_AGENT . "\r\nAccept: text/html\r\n",
                'follow_location' => 1,
                'max_redirects' => 3,
                'ignore_errors' => true,
            ],
            'https' => [
                'timeout' => self::TIMEOUT,
            ],
        ]);

        $stream = @fopen($url, 'rb', false, $context);
        if ($stream === false) {
            throw new RuntimeException('Could not fetch that URL.');
        }

        $data = '';
        while (!feof($stream) && strlen($data) < self::MAX_BYTES) {
            $chunk = fread($stream, 8192);
            if ($chunk === false) {
                break;
            }
            $data .= $chunk;
        }
        fclose($stream);

        return $data;
    }

    /**
     * @return array{0:?string,1:?string,2:?string}
     */
    private function parseMeta(string $html, string $baseUrl): array
    {
        if (trim($html) === '') {
            return [null, null, null];
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        $title = $this->metaContent($xpath, 'og:title');
        $description = $this->metaContent($xpath, 'og:description');
        $image = $this->metaContent($xpath, 'og:image');

        if (!$title) {
            $titleNodes = $doc->getElementsByTagName('title');
            if ($titleNodes->length > 0) {
                $title = trim($titleNodes->item(0)->textContent);
            }
        }

        if (!$description) {
            $descNode = $xpath->query('//meta[@name="description"]')->item(0);
            if ($descNode instanceof \DOMElement) {
                $description = trim($descNode->getAttribute('content'));
            }
        }

        if (!$image) {
            $imgNode = $xpath->query('//img[@src]')->item(0);
            if ($imgNode instanceof \DOMElement) {
                $image = $imgNode->getAttribute('src');
            }
        }

        if ($image) {
            $image = $this->resolveUrl($image, $baseUrl);
        }

        return [$title ?: null, $description ?: null, $image ?: null];
    }

    private function metaContent(\DOMXPath $xpath, string $property): ?string
    {
        $node = $xpath->query('//meta[@property="' . $property . '"]')->item(0)
            ?? $xpath->query('//meta[@name="' . $property . '"]')->item(0);
        if ($node instanceof \DOMElement) {
            $content = trim($node->getAttribute('content'));
            return $content !== '' ? $content : null;
        }
        return null;
    }

    private function resolveUrl(string $url, string $baseUrl): string
    {
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }
        $base = parse_url($baseUrl);
        $scheme = $base['scheme'] ?? 'https';
        $host = $base['host'] ?? '';
        if (str_starts_with($url, '//')) {
            return $scheme . ':' . $url;
        }
        if (str_starts_with($url, '/')) {
            return $scheme . '://' . $host . $url;
        }
        return $scheme . '://' . $host . '/' . $url;
    }
}
