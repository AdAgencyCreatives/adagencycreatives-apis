<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('friend_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->string('status')->default('pending')->comment("'pending', 'accepted', 'declined', 'cancelled'");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('friend_requests');
    }
};
