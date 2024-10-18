<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['post_id', 'event_date', 'event_time', 'location', 'capacity', 'audience_type', 'url', 'image_path', 'status_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
