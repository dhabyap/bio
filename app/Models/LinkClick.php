<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkClick extends Model
{
    protected $fillable = ['link_content_id', 'link_name', 'link_url', 'referer', 'user_agent', 'ip', 'source'];
}
