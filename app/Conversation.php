<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{   
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conversations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user1_id', 'user2_id', 'last_message_id', 'deleter_id', 'lastest_message', 'sent_time', 'user1_deleted_time', 'user2_deleted_time'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the message list.
     */
    public function messages()
    {
        return $this->hasMany('App\Message')->orderBy('sending_time', 'desc')->orderBy('id', 'desc');
    }
}
