<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkContent extends Model
{
    protected $fillable = ['state'];

    protected function casts(): array
    {
        return [
            'state' => 'array',
        ];
    }
}