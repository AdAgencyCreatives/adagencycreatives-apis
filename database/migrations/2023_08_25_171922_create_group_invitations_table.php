<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('inviter_user_id');
            $table->unsignedBigInteger('invitee_user_id');
            $table->unsignedBigInteger('group_id');
            $table->integer('status')->default(0)->comment('pending', 'accepted', 'declined');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_invitations');
    }
};
