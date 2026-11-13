<?php
namespace App\Services\Files;

use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use RuntimeException;
use Exception;

class CoverService
{
    // Gabarites for resizing source image
    private array $coverSize;

    // Number from 1 to 100
    private int $coverQuality;

    // The image format into which the source will need to be converted
    private ?string $coverFormat;

    function __construct(?int $width = null, ?int $height = null, ?int $quality = null, ?string $format = null) {
        $this->coverSize = [
            'width' => $width,
            'height' => $height,
        ];
        if (!$this->coverSize['width'] && config('services.cover.width')) {
            $this->coverSize['width'] = (int) config('services.cover.width');
        }
        if (!$this->coverSize['height'] && config('services.cover.height')) {
            $this->coverSize['height'] = (int) config('services.cover.height');
        }
        $this->coverQuality = (int) ($quality ? $quality : config('services.cover.quality', 80));
        $this->coverFormat = $format ? $format : config('services.cover.format', 'webp');
    }

    /**
     * Return cover path for image by path
     *
     * @param string $sourceImagePath
     * @param string $convertedImagePath
     * @return string|NULL
     */
    public function forImageByPath(string $sourceImagePath, ?string $convertedImagePath = null): ?string
    {
        if (!$convertedImagePath) {
            $convertedImagePath = $this->coverPathFromSourcePath($sourceImagePath);
        }

        // If file already exists, return it
        if (file_exists($convertedImagePath)) {
            return $convertedImagePath;
        }

        if (!file_exists($sourceImagePath)) {
            throw new RuntimeException("Image with path '$sourceImagePath' is not exists");
        }

        // Create image from path.
        // Read image. If image is not readable, return null
        try {
            $image = ImageManager::imagick()->read($sourceImagePath);
        } catch (Exception $e) {
            // Log to Sentry
            app(\App\Services\SentryService::class)?->captureException($e);
            // Return null
            return null;
        }

        // Resizing
        $image->scaleDown($this->coverSize['width'], $this->coverSize['height']);

        // Convert image in cover format
        $image->toWebp($this->coverQuality);

        // Saving
        $image->save($convertedImagePath);

        return $convertedImagePath;
    }

    public function coverPathFromSourcePath(string $sourcePath): ?string
    {
        if ($basePath = Str::beforeLast($sourcePath, '.')) {
            return $basePath . '_cover.'. $this->coverFormat;
        }
        return null;
    }
}