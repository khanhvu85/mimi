<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDelete extends Model
{
    protected $table = 'user_deletes';

    protected $fillable = ['user_id', 'reason', 'email', 'name', 'avatar', 'photos', 'dob', 'gender', 'introduce', 'phone', 'password', 'remember_token', 'latitude', 'longitude', 'chinese_id', 'address', 'job', 'socket_id', 'last_used_time', 'education', 'income', 'height', 'weight', 'match_with_verified'];

    protected $hidden = ['password', 'last_used_time', 'remember_token', 'created_at', 'updated_at', 'active_level', 'match_with_verified'];

}
