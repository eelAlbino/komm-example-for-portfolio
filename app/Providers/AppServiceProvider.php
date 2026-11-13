<?php

namespace App\Providers;

use App\Events\AttachmentDeletedEvent;
use App\Events\AttachmentToArrayConvertingEvent;
use App\Listeners\AttachmentDeletedListener;
use App\Listeners\AttachmentToArrayConvertingListener;
use App\Models\Attachment;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Orchid\Attachment\Models\Attachment as OrchidAttachment;
use Orchid\Platform\Dashboard;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Dashboard::useModel(OrchidAttachment::class, Attachment::class);
        Event::listen(AttachmentDeletedEvent::class, AttachmentDeletedListener::class);
        Event::listen(AttachmentToArrayConvertingEvent::class, AttachmentToArrayConvertingListener::class);
    }
}
