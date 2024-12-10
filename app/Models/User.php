<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'users';
    protected $primarykey = 'id';
    protected $fillable = [
        'name',
        'username',
        'password',
        'phone',
        'address',
        'role',
        'created_at',
        'updated_at'
    ];

    public function orders()
    {
        $this->hasMany('user_id', 'id');
    }
}
