<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unnotify extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unnotify';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'partner_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the partner.
     */
    public function partner()
    {
        return $this->belongsTo('App\User', 'partner_id');
    }
}
