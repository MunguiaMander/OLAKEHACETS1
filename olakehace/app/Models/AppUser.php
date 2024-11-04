<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AppUser extends Authenticatable
{
    protected $table = 'app_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status_id',
        'post_aprvd', 
    ];
    public $timestamps = false; 

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
