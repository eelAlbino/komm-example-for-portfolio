<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

use Orchid\Attachment\File;
use App\Traits\Attachable;
use App\Models\Attachment;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\AttachmentHelper;


class Comment extends Model
{
    use Filterable, AsSource, Chartable, HasFactory, Attachable;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'project_id',
        'parent_id',
        'status_id',
        'mentioned_users',
        'number',
        'text',
        'url',
        'title',
        'type',
        'selector_start',
        'selector_end',
        'fragment',
        'index_start',
        'index_end',
        'screen',
        'viewport',
        'ua',
        'os',
        'browser',
        'version',
        'device',
        'created_by',
        'is_private'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'mentioned_users' => 'array',
        'is_private' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'id'         => Where::class,
        'created_by' => Where::class,
        'project_id' => Where::class,
        'parent_id'  => Where::class,
        'status_id'  => Where::class,
        'number'     => Like::class,
        'is_private' => Where::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'created_by',
        'project_id',
        'parent_id',
        'status_id',
        'number',
        'updated_at',
        'created_at',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function status()
    {
        return $this->belongsTo(CommentStatus::class, 'status_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function attachmentRecursive(?array $filter = null): Collection
    {
        $result = $this->attachment;
        $result->each(function($item) {
            $item->comment_id = $this->id;
        });
        foreach ($this->children as $childItem) {
            $result = $result->merge($childItem->attachmentRecursive($filter));
        }
        if ($filter) {
            $result = $result->filter(function ($item, int $key) use ($filter) {
                $allow = true;
                if (isset($filter['type'])) {
                    $isMedia = app(AttachmentHelper::class)->isMedia($item);
                    // All media files
                    if ($filter['type'] == 'media') {
                        $allow = $isMedia;
                    }
                    // All not media files
                    if ($filter['type'] == 'files') {
                        $allow = !$isMedia;
                    }
                }
                return $allow;
            });
        }
        return $result;
    }

    public function uploadFile(UploadedFile $uploadedFile) {
        $attachment = DB::transaction(function() use ($uploadedFile) {
            $attachment = (new File($uploadedFile))->allowDuplicates()->load();
            $this->attachment()->syncWithoutDetaching([
                $attachment->id
            ]);
            return $attachment;
        });
        // Recalc organisation storage size
        $this->project?->organisation->recalcStorageSize();
        return $attachment;
    }
}
