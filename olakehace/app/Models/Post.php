<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id', 'category_id', 'title', 'description', 'status_id', 'reports_count'];

    public function user()
    {
        return $this->belongsTo(AppUser::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
