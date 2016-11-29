<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            //register informations
            $table->increments('id');
            $table->string('phone', 100)->unique();
            $table->string('password');

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

            $table->rememberToken();
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
        Schema::drop('users');
    }
}
