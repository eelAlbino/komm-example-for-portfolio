<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;
use App\Contracts\Docs\Swagger\Models\ProjectRole as SwaggerDoc;

class ProjectRole extends Model implements SwaggerDoc
{
    use Filterable, AsSource, Chartable, HasFactory;

    const ADMIN_ID       = 1;
    const MANAGER_ID     = 2;
    const PARTICIPANT_ID = 3;
    const GUEST_ID       = 4;
    
    /**
     * @var string
     */
    protected $table = 'project_roles';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'permissions',
        'weight'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'id'          => Like::class,
        'name'        => Like::class,
        'slug'        => Like::class,
        'permissions' => Like::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'slug',
        'updated_at',
        'created_at',
    ];

    /**
     * Get list of the project roles
     * 
     * @return array
     */
    public function indexData() {
        $projectRoles = ProjectRole::all();
        
        return $projectRoles;
    }

    /**
     * Get the project users
     *
     * @return HasMany|Collection|ProjectUser[]
     */
    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'project_role_id');
    }
}
