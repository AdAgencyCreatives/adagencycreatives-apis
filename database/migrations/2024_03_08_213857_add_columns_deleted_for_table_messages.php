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
        Schema::table('messages', function (Blueprint $table) {
            //
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('receiver_deleted_at')->nullable();
            $table->timestamp('sender_conversation_deleted_at')->nullable();
            $table->timestamp('receiver_conversation_deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            //
            $table->dropColumn('sender_deleted_at');
            $table->dropColumn('receiver_deleted_at');
            $table->dropColumn('sender_conversation_deleted_at');
            $table->dropColumn('receiver_conversation_deleted_at');
        });
    }
};
