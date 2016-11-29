<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 20);
            $table->integer('conversation_id');
            $table->integer('sender_id');
            $table->integer('reciever_id');
            $table->text('message');
            $table->dateTime('sending_time');
            $table->boolean('is_read')->default(false);
            $table->integer('deleter_id')->default(0);

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
        Schema::drop('messages');
    }
}
