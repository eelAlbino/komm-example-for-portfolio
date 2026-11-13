<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Organisation extends Model
{
    use Filterable, AsSource, Chartable, HasFactory;

    /**
     * @var string
     */
    protected $table = 'organisations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'created_by',
        'is_frozen',
        'freeze_reasons',
        'manager_id',
        'storage_size',
        'feature_flags'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalcStorageSize(): int
    {
        $organisationId = $this->id;
        $size = DB::table('attachments')
            ->join('attachmentable', 'attachments.id', '=', 'attachmentable.attachment_id')
            ->leftJoin('projects', function ($join) {
                $join->on('attachmentable.attachmentable_id', '=', 'projects.id')
                ->where('attachmentable.attachmentable_type', '=', Project::class);
            })
            ->leftJoin('comments', function ($join) {
                $join->on('attachmentable.attachmentable_id', '=', 'comments.id')
                ->where('attachmentable.attachmentable_type', '=', Comment::class);
            })
            ->leftJoin('projects as comment_projects', 'comments.project_id', '=', 'comment_projects.id')
            ->where(function ($query) use ($organisationId) {
                $query->where(function ($query) use ($organisationId) {
                    $query->where('projects.organisation_id', $organisationId)
                        ->whereNull('projects.archived_at');
                })
                ->orWhere('comment_projects.organisation_id', $organisationId);
            })
            ->sum('attachments.size'); 

        $this->storage_size = $size;
        $this->save();
        return $size;
    }
}
