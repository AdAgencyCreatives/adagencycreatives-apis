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
        Schema::create('creative_spotlights', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name')->nullable();
            $table->string('title')->nullable();
            $table->string('path');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('status')->default(0)->comment('0:pending, 1:approved, 2:rejected');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creative_spotlights');
    }
};
