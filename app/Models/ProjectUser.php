<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;



class ProjectUser extends Model
{
    use Filterable, AsSource, Chartable, HasFactory;

    /**
     * @var string
     */
    protected $table = 'project_users';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'created_by',
        'project_id',
        'user_id',
        'project_role_id',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'id'              => Where::class,
        'project_id'      => Like::class,
        'user_id'         => Like::class,
        'project_role_id' => Like::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'project_id',
        'user_id',
        'project_role_id',
        'updated_at',
        'created_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project_role()
    {
        return $this->belongsTo(ProjectRole::class, 'project_role_id');
    }

    public function projectRole()
    {
        return $this->belongsTo(ProjectRole::class, 'project_role_id');
    }
}
