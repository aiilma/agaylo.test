<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->text('body');
            $table->string('attachment', 45)->nullable();
            $table->unsignedInteger('request_id');
            $table->unsignedInteger('author_id');
            $table->boolean('is_checked')->default(0);

            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_messages');
    }
}
