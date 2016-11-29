<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionaries', function (Blueprint $table) {
            //declare informations
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('question_id');
            $table->integer('mine_answer')->default(-1);
            $table->integer('partner_answer')->default(-1);
            $table->integer('hidemefromhomescreen');
            $table->integer('show_male');
            $table->integer('show_female');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('questionaries');
    }
}
