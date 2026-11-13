<?php

namespace App\Models;

use Orchid\Attachment\Models\Attachment as BaseAttachment;
use App\Events\UserUpdatedEvent;
use App\Events\AttachmentToArrayConvertingEvent;
use App\Events\AttachmentDeletedEvent;
use App\Events\AttachmentCreatedEvent;
use App\Events\AttachmentUpdatedEvent;
use App\Events\AttachmentSavedEvent;
use App\Traits\ToArrayHasEvents;
use App\Contracts\Docs\Swagger\Models\Attachment as SwaggerDoc;

class Attachment extends BaseAttachment implements SwaggerDoc
{
    use ToArrayHasEvents;

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'array_converting' => AttachmentToArrayConvertingEvent::class,
        'deleted' => AttachmentDeletedEvent::class,
        'created' => AttachmentCreatedEvent::class,
        'updated' => AttachmentUpdatedEvent::class,
        'saved' => AttachmentSavedEvent::class
    ];

    /**
     * Init recalculate storage size for related organisations
     */
    public function initRecalcStorageSize(): void
    {
        if (!$this->relationships) {
            return ;
        }
        $this->relationships->each(function($attachable) {
            $organisation = match ($attachable->attachmentable_type) {
                Comment::class => Comment::find($attachable->attachmentable_id)?->project?->organisation,
                Project::class => Project::find($attachable->attachmentable_id)?->organisation,
                default => null
            };
            if ($organisation) {
                $organisation->recalcStorageSize();
            }
        });
    }
}