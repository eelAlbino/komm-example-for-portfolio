<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;

class CommentAttachmentArchive extends Model
{
    use HasFactory, Attachable;

    /**
     * @var string
     */
    protected $table = 'comment_attachment_archives';

    /**
     * @var array
     */
    protected $fillable = [
        'comment_id',
        'filter_hash',
        'content_control_sum',
        'showed_at'
    ];

    protected $dates = [
        'showed_at'
    ];

    /**
     * Get the comment
     *
     * @return BelongsTo|Comment
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
