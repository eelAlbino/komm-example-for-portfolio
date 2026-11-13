<?php

use App\Http\Controllers\Api\V1\CommentController;
use Illuminate\Support\Facades\Route;

Route::controller(CommentController::class)->group(function () {
    Route::get('comment/{id}/file/zip', 'getFilesZip');
});
