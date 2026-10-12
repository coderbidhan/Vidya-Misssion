<?php

namespace App\Services;

class GoogleDriveService
{
    /**
     * Generate an embeddable preview URL for a Google Drive video file.
     *
     * This URL is safe to expose to the client — it does not allow direct download.
     *
     * @param  string  $fileId  The Google Drive file ID.
     * @return string The embed/preview URL.
     */
    public function getEmbedUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    /**
     * Generate a direct (non-download) view URL for a Google Drive file.
     *
     * NOTE: This URL is for internal use only and should NOT be sent to students.
     *
     * @param  string  $fileId  The Google Drive file ID.
     * @return string The direct view URL.
     */
    public function getDirectUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/view";
    }

    /**
     * Generate a thumbnail URL for a Google Drive file.
     *
     * @param  string  $fileId  The Google Drive file ID.
     * @param  int  $size  Thumbnail size in pixels (default 320).
     */
    public function getThumbnailUrl(string $fileId, int $size = 320): string
    {
        return "https://drive.google.com/thumbnail?id={$fileId}&sz=s{$size}";
    }
}
