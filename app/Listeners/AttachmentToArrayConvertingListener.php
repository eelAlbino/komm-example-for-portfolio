<?php

namespace App\Listeners;

use App\Helpers\AttachmentHelper;

class AttachmentToArrayConvertingListener
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
     * Return attachment in project format.
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        return app(AttachmentHelper::class)->toArray($event->attachment);
    }

}
