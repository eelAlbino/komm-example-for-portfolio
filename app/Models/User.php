<?php

namespace App\Models;

use App\Traits\HasProjectPermissions;
use Orchid\Platform\Models\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasProjectPermissions;

    protected $attributes = [
        'notifications' => '{"email":{"comments":true,"replies":true,"mentions":true,"period":"daily"},"browser":{"comments":true,"replies":true,"mentions":true,"period":"immediately"}}',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
