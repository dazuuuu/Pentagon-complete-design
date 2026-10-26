<?php

namespace App\Helpers;

class Upload
{
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    private const MAX_BYTES = 25 * 1024 * 1024;

    /** Longest edge (px) uploaded images are scaled down to before saving. */
    private const MAX_DIMENSION = 2000;

    private const ALLOWED_VIDEO_MIME = [
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/quicktime' => 'mov',
    ];

    private const MAX_VIDEO_BYTES = 80 * 1024 * 1024;

    /**
     * Store a single uploaded file under public/assets/images/uploads/{subdir}/.
     * Returns the web-relative path, or null if no file was submitted.
     * Throws \RuntimeException on invalid/oversized/failed uploads.
     */
    public static function store(array $file, string $subdir): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(self::errorMessage((int) $file['error'], $file['name'] ?? 'Image'));
        }

        if ($file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('Image exceeds the 25MB size limit.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Invalid upload.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED_MIME[$mime])) {
            throw new \RuntimeException('Only JPG, PNG, GIF, or WEBP images are allowed.');
        }

        $ext = self::ALLOWED_MIME[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $subdir = trim($subdir, '/\\');
        $destDir = Path::publicPath('assets', 'images', 'uploads', $subdir);

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $basename = bin2hex(random_bytes(16));
        if ($mime !== 'image/gif' && ($optimized = self::optimize($file['tmp_name'], $destDir, $basename)) !== null) {
            return 'assets/images/uploads/' . $subdir . '/' . $optimized;
        }

        $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new \RuntimeException('Could not save uploaded file.');
        }

        return 'assets/images/uploads/' . $subdir . '/' . $filename;
    }

    /**
     * Store multiple files from a `<input type="file" name="x[]" multiple>` field.
     * $files is the raw $_FILES['x'] superglobal entry. Returns array of web-relative paths.
     */
    public static function storeMany(array $files, string $subdir): array
    {
        $paths = [];

        if (!isset($files['name']) || !is_array($files['name'])) {
            return $paths;
        }

        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $single = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];

            try {
                $path = self::store($single, $subdir);
            } catch (\RuntimeException $e) {
                // All-or-nothing: don't leave earlier files of this batch orphaned.
                foreach ($paths as $stored) {
                    self::delete($stored);
                }
                throw $e;
            }
            if ($path !== null) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Store a single uploaded video under public/assets/videos/uploads/{subdir}/.
     * Returns the web-relative path, or null if no file was submitted.
     */
    public static function storeVideo(array $file, string $subdir): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(self::errorMessage((int) $file['error'], $file['name'] ?? 'Video'));
        }

        if ($file['size'] > self::MAX_VIDEO_BYTES) {
            throw new \RuntimeException('Video exceeds the 80MB size limit.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Invalid video upload.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED_VIDEO_MIME[$mime])) {
            throw new \RuntimeException('Only MP4, WEBM, or MOV videos are allowed.');
        }

        $ext = self::ALLOWED_VIDEO_MIME[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $subdir = trim($subdir, '/\\');
        $destDir = Path::publicPath('assets', 'videos', 'uploads', $subdir);

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new \RuntimeException('Could not save uploaded video.');
        }

        return 'assets/videos/uploads/' . $subdir . '/' . $filename;
    }

    /**
     * True when PHP silently discarded the whole request body because it was
     * bigger than post_max_size — $_POST and $_FILES both arrive empty, so the
     * CSRF check fails and nothing would be saved without telling the admin.
     */
    public static function postTooLarge(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
            && $_POST === []
            && $_FILES === []
            && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
    }

    public static function postTooLargeMessage(): string
    {
        return 'The upload was too large for the server (limit: ' . ini_get('post_max_size')
            . '). Upload fewer or smaller images at a time.';
    }

    private static function errorMessage(int $code, string $name): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => $name . ' is larger than the server allows ('
                . ini_get('upload_max_filesize') . ' per file).',
            UPLOAD_ERR_PARTIAL => $name . ' only partially uploaded — please try again.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => 'The server could not save ' . $name . ' (temporary folder not writable).',
            default => 'Upload of ' . $name . ' failed (error code ' . $code . ').',
        };
    }

    /**
     * Scale an image down to MAX_DIMENSION and re-encode it (WebP when
     * available, else JPEG/PNG) so large phone photos and Canva exports load
     * quickly on the site. Returns the saved filename, or null to fall back to
     * storing the original untouched.
     */
    private static function optimize(string $tmpPath, string $destDir, string $basename): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }

        $info = @getimagesize($tmpPath);
        if (!$info || $info[0] < 1 || $info[1] < 1) {
            return null;
        }
        [$width, $height] = $info;

        // Decoding needs ~5 bytes per pixel; raise the limit for big photos.
        $needed = (int) ($width * $height * 5) + 64 * 1024 * 1024;
        $limit = ini_get('memory_limit');
        if ($limit !== '-1' && $needed > self::bytes((string) $limit)) {
            @ini_set('memory_limit', (string) $needed);
        }

        $source = @imagecreatefromstring((string) file_get_contents($tmpPath));
        if ($source === false) {
            return null;
        }

        $scale = min(1, self::MAX_DIMENSION / max($width, $height));
        $newW = max(1, (int) round($width * $scale));
        $newH = max(1, (int) round($height * $scale));

        $image = imagecreatetruecolor($newW, $newH);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagecopyresampled($image, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
        unset($source);

        $isPng = $info[2] === IMAGETYPE_PNG;
        if (function_exists('imagewebp')) {
            $filename = $basename . '.webp';
            $ok = imagewebp($image, $destDir . DIRECTORY_SEPARATOR . $filename, 82);
        } elseif ($isPng) {
            $filename = $basename . '.png';
            $ok = imagepng($image, $destDir . DIRECTORY_SEPARATOR . $filename, 7);
        } else {
            $filename = $basename . '.jpg';
            $ok = imagejpeg($image, $destDir . DIRECTORY_SEPARATOR . $filename, 85);
        }
        unset($image);

        return $ok ? $filename : null;
    }

    private static function bytes(string $value): int
    {
        $value = trim($value);
        $number = (int) $value;
        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /**
     * Delete a previously stored file given its web-relative path.
     */
    public static function delete(string $relativePath): void
    {
        $full = Path::publicPath(...explode('/', ltrim($relativePath, '/')));
        if (is_file($full)) {
            unlink($full);
        }
    }
}
