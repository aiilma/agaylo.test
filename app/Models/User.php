<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function isManager()
    {
        foreach ($this->roles as $role) {
            if ($role->name === 'manager') return true;
        }

        return false;
    }


    /* RELATIONS */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'client_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(RequestMessage::class, 'author_id', 'id');
    }
}
