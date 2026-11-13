<?php

return [
    'attachment' => [
        'disk' => env('FILESYSTEM_DISK', 'public'),
        'generator' => Orchid\Attachment\Engines\Generator::class,
    ],
];
