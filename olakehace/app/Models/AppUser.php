<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    protected $table = 'app_users';
    protected $fillable = ['name', 'email', 'password', 'role_id', 'status_id', 'post_aprvd'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
