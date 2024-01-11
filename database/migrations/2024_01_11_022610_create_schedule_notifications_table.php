<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('sender_id')->index();
            $table->unsignedBigInteger('recipient_id')->index();
            $table->unsignedBigInteger('post_id')->index();
            $table->string('notification_text');
            $table->string('status')->default('0')->comment("'pending : 0', 'delivered : 1'")->index();
            $table->string('type')->default('0')->comment("'create_post : 0', 'comment : 1'")->index();
            $table->timestamp('scheduled_at');
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
        Schema::dropIfExists('schedule_notifications');
    }
};
