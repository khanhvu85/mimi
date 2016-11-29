<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDeletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_deletes', function (Blueprint $table) {
            $table->increments('id');
            //register informations
            $table->integer('user_id');
            $table->string('phone', 100)->unique();
            $table->string('password');
            $table->string('reason');

            //necessary informations
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->text('photos')->nullable();
            $table->text('introduce')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 20)->nullable();

            $table->string('email')->nullable()->unique();	//optional

            //additional informations
            $table->string('education')->nullable();
            $table->string('income')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();

            //another informations
            $table->dateTime('last_used_time')->nullable();
            $table->tinyInteger('active_level')->default(1);
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
        Schema::drop('user_deletes');
    }
}
