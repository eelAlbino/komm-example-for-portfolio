<?php

namespace App\Listeners;

use App\Events\ProjectCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use App\Services\Files\CoverService;
use App\Events\AttachmentDeletedEvent;

class AttachmentDeletedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\AttachmentDeletedEvent  $event
     * @return void
     */
    public function handle(AttachmentDeletedEvent $event)
    {
        $attachment = $event->attachment;
        $this->dropCover($attachment);
        $attachment->initRecalcStorageSize();
    }

    /**
     * Delete cover file if exists.
     *
     * @param Attachment $attachment
     */
    private function dropCover(Attachment $attachment) {
        // Storage disk of attachment
        $storageDisk = Storage::disk($attachment->disk);
        // Attachment path
        $attachmentPath = $storageDisk->path($attachment->physicalPath());
        // Cover path
        $coverPath = app(CoverService::class)->coverPathFromSourcePath($attachmentPath);
        // If file exists unlink
        if (file_exists($coverPath)) {
            unlink($coverPath);
        }
    }
}
