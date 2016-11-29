<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifyID extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verify_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['phone', 'description', 'image'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

}
