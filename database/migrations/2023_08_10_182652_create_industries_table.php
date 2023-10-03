<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('slug')->nullable();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('industries');
    }
};
