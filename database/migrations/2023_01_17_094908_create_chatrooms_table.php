<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatrooms', function (Blueprint $table) {
            $table->id();
            $table->unique(array('loginuserid', 'chatuserid'));
            $table->unsignedBigInteger('loginuserid');
            $table->foreign('loginuserid')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('chatuserid');
            $table->foreign('chatuserid')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('chatrooms');
    }
}
