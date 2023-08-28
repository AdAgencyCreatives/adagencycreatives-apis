<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('group_id');
            $table->integer('role')->default(0)->comment('1: admin, 2: moderator, 3: member');
            $table->timestamp('joined_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_members');
    }
};
