<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Comment;
use App\Models\User;
use App\Enums\ExceptionCodes\CommentPolicyEnum as ExceptionEnum;
use Orchid\Attachment\Models\Attachment;

class CommentPolicy
{

    public function view(User $user, Comment $comment)
    {
        if(!$comment->exists()) {
            return Response::deny('Comment not found.');
        }
        // Check user in project
        if ($response = $this->checkInProject($user, $comment)) {
            return $response;
        }
        // Check user private permission
        if ($response = $this->checkPrivateAllow($user, $comment)) {
            return $response;
        }
        return true;
    }

    private function checkInProject(User $user, Comment $comment): ?Response
    {
        if (!$user->isInProject($comment->project_id)) {
            return Response::deny(
                'User is not in comment project.',
                ExceptionEnum::NOT_IN_PROJECT
            );
        }
        return null;
    }

    private function checkPrivateAllow(User $user, Comment $comment): ?Response
    {
        if ($comment->is_private && !$user->hasProjectPermissions($comment->project_id, ['comment.private'])) {
            return Response::deny(
                'Private comments unavailable for user.',
                ExceptionEnum::PRIVATE_FEATURE_UNAVAILABLE_FOR_USER
            );
        }
        return null;
    }
}
