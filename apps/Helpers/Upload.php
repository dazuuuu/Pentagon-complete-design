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

    private const MAX_BYTES = 8 * 1024 * 1024;

    /**
     * Store a single uploaded file under assets/images/uploads/{subdir}/.
     * Returns the web-relative path, or null if no file was submitted.
     * Throws \RuntimeException on invalid/oversized/failed uploads.
     */
    public static function store(array $file, string $subdir): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload failed (error code ' . $file['error'] . ').');
        }

        if ($file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('Image exceeds the 8MB size limit.');
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
        $destDir = Path::join('assets', 'images', 'uploads', $subdir);

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
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

            $path = self::store($single, $subdir);
            if ($path !== null) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Delete a previously stored file given its web-relative path.
     */
    public static function delete(string $relativePath): void
    {
        $full = Path::join(...explode('/', ltrim($relativePath, '/')));
        if (is_file($full)) {
            unlink($full);
        }
    }
}
