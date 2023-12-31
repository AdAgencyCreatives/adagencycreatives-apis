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
        Schema::disableForeignKeyConstraints();

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('topic_id')->constrained();
            $table->string('description');
            $table->string('link');
            $table->string('preview_link')->nullable();
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources');
    }
};