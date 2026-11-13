<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\CommentAttachmentArchive;

class CommentAttachmentArchiveFactory extends Factory
{

    public function definition()
    {
        return [
            'comment_id' => Comment::factory(),
            'filter_hash' => $this->faker->md5,
            'content_control_sum' => $this->faker->md5,
            'showed_at' => now(),
            'status' => CommentAttachmentArchive::STATUS_INIT
        ];
    }
}
