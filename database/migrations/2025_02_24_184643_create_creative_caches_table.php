<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('creative_caches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creative_id')->index();
            $table->string('category')->index()->nullable();
            $table->string('location')->index()->nullable();
            $table->integer('activity_rank')->index();
            $table->timestamp('created_at')->index();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('creative_caches');
    }
};
