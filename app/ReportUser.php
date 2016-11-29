<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'partner_id', 'comment'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}
