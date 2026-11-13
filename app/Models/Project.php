<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;
use App\Traits\Attachable;
use App\Models\Attachment;

use Illuminate\Support\Str;


class Project extends Model
{
    use Filterable, AsSource, Chartable, HasFactory, Attachable;

    /**
     * Attachment groups
     */
    const ATTACHMENT_GROUP_COVER = 'cover';
    const ATTACHMENT_GROUP_FAVICON = 'favicon';

    /**
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'organisation_id',
        'name',
        'description',
        'prefix',
        'url',
        'additional_urls',
        'client',
        'widget',
        'widget_status',
        'widget_checked_at',
        'widget_no_checks_until',
        'archived_by',
        'archived_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // TODO: add actual types
        //'widget_status'         =>'boolean',
        'widget_checked_at'         => 'datetime',
        'widget_no_checks_until'    => 'datetime',
        'additional_urls'           => 'array'
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id'              => Where::class,
        'created_by'      => Where::class,
        'organisation_id' => Where::class,
        'name'            => Like::class,
        'prefix'          => Like::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'created_by',
        'organisation_id',
        'name',
        'prefix',
        'updated_at',
        'created_at',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'project_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users', 'project_id', 'user_id')
            ->withPivot(['project_role_id']);
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'project_id');
    }
}
