<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questionary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questionaries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'question_id', 'mine_answer', 'partner_answer', 'hidemefromhomescreen', 'show_male', 'show_female'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}
