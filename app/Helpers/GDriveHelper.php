<?php

namespace App\Helpers;

class GDriveHelper
{
    /**
     * Extracts the file ID from various Google Drive sharing link formats.
     * 
     * Supported formats:
     * - https://drive.google.com/open?id=FILE_ID
     * - https://drive.google.com/file/d/FILE_ID/view
     * - https://drive.google.com/uc?id=FILE_ID
     */
    public static function getFileId(string $url): ?string
    {
        // Format: open?id=... or uc?id=...
        if (preg_match('/[?&]id=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }

        // Format: /file/d/...
        if (preg_match('/\/file\/d\/([^\/?&#]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Converts a Google Drive link into a direct download URL.
     */
    public static function getDirectDownloadUrl(string $url): ?string
    {
        $id = self::getFileId($url);

        if (!$id) {
            return null;
        }

        return "https://drive.google.com/uc?export=download&id={$id}";
    }
}
