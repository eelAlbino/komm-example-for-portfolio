<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetFilesZipCommentRequest;
use App\Models\Comment;
use App\Models\CommentAttachmentArchive;
use App\Services\Comments\AttachmentArchiveService;

class CommentController extends Controller
{
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->middleware('auth:api')->except('getStatuses');
        $this->comment = $comment;
    }

    public function getFilesZip($id, GetFilesZipCommentRequest $request)
    {
        $comment = Comment::findOrFail($id);

        $this->authorize('view', $comment);
        $filter = null;
        if ($only = $request->get('only')) {
            $filter = [
                'type' => $only
            ];
        }
        $archiveElem = app(AttachmentArchiveService::class, ['comment' => $comment])->get($filter);
        if ($archiveElem) {
            $result = [
                'status' => 'success',
                'filling_status' => $archiveElem->status
            ];
            if ($archiveElem->status === CommentAttachmentArchive::STATUS_FILLED) {
                $result['archive'] = $archiveElem->archive_attachment?->only(['original_name', 'url', 'extension', 'size']);
                // Set showed_at
                $archiveElem->showed_at = now();
                $archiveElem->save();
            }
            // Response
            return response()->json($result);
        }
        abort(500, 'Archive not generated');
    }
}
