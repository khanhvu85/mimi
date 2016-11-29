<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'conversation_id', 'sender_id', 'reciever_id', 'message', 'sending_time', 'is_read', 'deleter_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleter_id', 'conversation_id'];

    /**
     * Get the conversation.
     */
    public function conversation()
    {
        return $this->belongsTo('App\Conversation');
    }
}
