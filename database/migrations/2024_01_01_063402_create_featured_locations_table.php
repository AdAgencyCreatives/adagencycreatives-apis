<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('featured_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id');
            $table->string('preview_link')->nullable();
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('featured_locations');
    }
};
