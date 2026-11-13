<?php

namespace App\Helpers;

use Orchid\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use App\Services\Files\CoverService;
use App\Services\Files\VideoThumbnailService;

class AttachmentHelper
{

    public function __construct(private Attachment $attachment)
    {
    }

    /**
     * Media file mime types for attachment
     * @return array
     */
    public function mediaMimeTypes(): array
    {
        return array_merge(
            $this->imageMimeTypes(),
            $this->videoMimeTypes(),
        );
    }

    /**
     * Check attachment type is media
     *
     * @return boolean
     */
    public function isMedia(?Attachment $attachment = null): bool
    {
        return $this->isImage($attachment) || $this->isVideo($attachment);
    }

    /**
     * Check attachment type is video by mime type
     *
     * @return boolean
     */
    public function isVideo(?Attachment $attachment = null): bool
    {
        return in_array(($attachment ?: $this->attachment)->getMimeType(), $this->videoMimeTypes());
    }

    /**
     * Check attachment type is image by mime type
     *
     * @return boolean
     */
    public function isImage(?Attachment $attachment = null): bool
    {
        return in_array(($attachment ?: $this->attachment)->getMimeType(), $this->imageMimeTypes());
    }

    /**
     * Video file mime types for attachment
     *
     * @return array
     */
    public function videoMimeTypes(): array
    {
        return [
            'video/mp4',
            'video/webm'
        ];
    }

    /**
     * Image file mime types for attachment
     * @return array
     */
    public function imageMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/svg+xml',
            'image/gif',
            'image/vnd.microsoft.icon'// ICO
        ];
    }

    /**
     * Convert Attachment to array using method attachmentToArray
     *
     * @param Attachment|Collection|null $attachment
     * @return array
     */
    public function toArray(mixed $attachment = null): array
    {
        // If collection, transformation each item
        if (is_a($attachment, Collection::class)) {
            $result = [];
            $attachment->each(function($item) use (&$result) {
                $result[]= $this->toArray($item);
            });
            return $result;
        }

        if (!$attachment) {
            $attachment = $this->attachment;
        }
        return $this->attachmentToArray($attachment);
    }

    /**
     * Convert Attachment to array.
     *
     * @param Attachment $attachment
     * @return array
     */
    private function attachmentToArray(Attachment $attachment): array
    {
        $result = $attachment->only(['id', 'original_name', 'url', 'extension', 'size']);
        $result['cover'] = $this->cover($attachment);
        $result['is_media'] = $this->isMedia($attachment);
        return $result;
    }

    /**
     * Get image cover link
     * @param Attachment $attachment
     * @return string|null
     */
    private function cover(Attachment $attachment): ?string
    {
        $imagePathForCover = null;
        // Storage disk of attachment
        $storageDisk = Storage::disk($attachment->disk);
        // Attachment path
        $attachmentPath = $storageDisk->path($attachment->physicalPath());
        // If file is image, get image path
        if ($this->isImage($attachment)) {
            $imagePathForCover = $attachmentPath;
        }
        // If file is image, get thumbnail image path
        if ($this->isVideo($attachment)) {
            $imagePathForCover = $this->videoThumbnailImagePath($attachmentPath);
        }
        $coverPath = null;
        // If image path exists, create cover path
        if ($imagePathForCover && file_exists($imagePathForCover)) {
            $coverPath = app(CoverService::class)->forImageByPath($imagePathForCover);
        }
        // If cover path created, return link on cover path
        if ($coverPath) {
            return $storageDisk->url(
                str_replace($storageDisk->path(''), '', $coverPath)
            );
        }
        return null;
    }

    /**
     * Return thumbnail image path for video by path.
     *
     * @param string $sourceVideoPath
     * @return string|NULL
     */
    private function videoThumbnailImagePath(string $sourceVideoPath): ?string
    {
        $fromSeconds = 5;
        // Get temp image file path from source path.
        $thumbnailImagePath = tempnam(sys_get_temp_dir(), hash('sha256', $sourceVideoPath) .'_'. $fromSeconds) . '.jpg';

        // Create thumbnail
        return app(VideoThumbnailService::class)->generate(
            $sourceVideoPath,
            $thumbnailImagePath
        );
    }
}