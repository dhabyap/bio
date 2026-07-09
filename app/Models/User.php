<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'username', 'name', 'email', 'password', 'avatar', 'bio',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function linkContent()
    {
        return $this->hasOne(LinkContent::class);
    }

    public function linkClicks()
    {
        return $this->hasManyThrough(LinkClick::class, LinkContent::class);
    }
}