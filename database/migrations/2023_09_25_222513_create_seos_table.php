<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('title');
            $table->string('description');
            $table->string('keywords');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seos');
    }
};
