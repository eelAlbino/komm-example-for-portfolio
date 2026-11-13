<?php
namespace App\Services\Files;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

class VideoThumbnailService
{

    /**
     * Generate thumbnail for video by path.
     *
     * @param string $videoPath
     * @param string $outputPath
     * @param int $fromSeconds
     * @return string
     */
    public function generate(string $videoPath, string $outputPath, int $fromSeconds = 2): string
    {
        // Initialize FFMpeg
        $ffmpeg = FFMpeg::create();

        // Open the video file
        $video = $ffmpeg->open($videoPath);

        // Generate a thumbnail at the specified time
        $video->frame(TimeCode::fromSeconds($fromSeconds))->save($outputPath);

        return $outputPath;
    }
}