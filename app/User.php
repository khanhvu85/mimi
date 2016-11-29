<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract 
{
    use Authenticatable, CanResetPassword;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'name', 'avatar', 'photos', 'dob', 'gender', 'introduce', 'phone', 'password', 'remember_token', 'latitude', 'longitude', 'chinese_id', 'address', 'job', 'socket_id', 'last_used_time', 'education', 'income', 'height', 'weight', 'match_with_verified'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'last_used_time', 'remember_token', 'active_level', 'match_with_verified'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the favourite list.
     */
    public function favourites()
    {
        return $this->hasMany('App\Favourite')->orderBy('created_at', 'desc');
    }

    /**
     * Get the blocked list.
     */
    public function blockeds()
    {
        return $this->hasMany('App\Block');
    }

    /**
     * Get the answer list.
     */
    public function answers()
    {
        return $this->hasMany('App\Questionary')->orderBy('question_id');
    }

    /**
     * Get the devices list.
     */
    public function device()
    {
        return $this->hasOne('App\Device');
    }

    /**
     * Get the send messages.
     */
    public function sendMessages()
    {
        return $this->hasMany('App\Message', 'sender_id');
    }

    /**
     * Get the recieved messages.
     */
    public function recievedMessages()
    {
        return $this->hasMany('App\Message', 'reciever_id');
    }

    /**
     * Get the send conversations.
     */
    public function sendConversations()
    {
        return $this->hasMany('App\Conversation', 'user1_id');
    }

    /**
     * Get the recieved messages.
     */
    public function recievedConversations()
    {
        return $this->hasMany('App\Conversation', 'user2_id');
    }

    /**
     * Get the setting of Push Notification.
     */
    public function userSetting()
    {
        return $this->hasOne('App\UserSetting');
    }

    /**
     * Get the blocked list.
     */
    public function unNotifies()
    {
        return $this->hasMany('App\Unnotify');
    }
}
