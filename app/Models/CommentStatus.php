<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;
use App\Contracts\Docs\Swagger\Models\CommentStatus as SwaggerDoc;

class CommentStatus extends Model implements SwaggerDoc
{
    use Filterable, AsSource, Chartable, HasFactory;
    
    /**
     * @var string
     */
    protected $table = 'comment_statuses';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'color',
    ];

    /**
     * @var array
     */
    protected $casts = [

    ];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'id'          => Like::class,
        'name'        => Like::class,
        'slug'        => Like::class,
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
}
