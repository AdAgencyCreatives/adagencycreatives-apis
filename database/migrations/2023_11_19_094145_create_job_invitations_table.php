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
        Schema::create('job_invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('creative_id')->index();
            $table->unsignedBigInteger('job_id')->index();
            $table->string('status')->nullable()->index();
            $table->timestamps();

            $table->unique(['job_id', 'creative_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_invitations');
    }
};