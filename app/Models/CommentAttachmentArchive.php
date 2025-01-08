<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Orchid\Attachment\Models\Attachment;
use App\Traits\Attachable;
use App\Contracts\Docs\Swagger\Models\CommentAttachmentArchive as SwaggerDoc;

class CommentAttachmentArchive extends Model implements SwaggerDoc
{
    use HasFactory, Attachable;

     const STATUS_INIT = 'init';
     const STATUS_FILLING = 'filling';
     const STATUS_FILLED = 'filled';

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
        'showed_at',
        'status'
    ];

    protected $dates = [
        'showed_at'
    ];

    /**
     * The attributes that should be appends.
     *
     * @var array
     */
    protected $appends = [
        'archive_attachment',
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

    /**
     * Get the comment's attachment.
     *
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function commentAttachments(?array $filter = null): Collection
    {
        return $this->comment->attachmentRecursive($filter);
    }

    /**
     * Get archive attachment file
     *
     * @return \Orchid\Attachment\Models\Attachment|null
     */
    public function getArchiveAttachmentAttribute(): ?Attachment
    {
        return $this->attachment()->first();
    }
}
