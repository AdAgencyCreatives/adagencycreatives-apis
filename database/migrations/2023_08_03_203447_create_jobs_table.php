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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->text('description');
            $table->string('employement_type');
            $table->text('industry_experience');
            $table->string('media_experience');
            $table->string('salary_range');
            $table->string('experience');
            $table->string('apply_type');
            $table->string('external_link')->nullable();
            $table->integer('status');
            $table->boolean('is_remote');
            $table->boolean('is_hybrid');
            $table->boolean('is_onsite');
            $table->boolean('is_featured');
            $table->boolean('is_urgent');
            $table->timestamp('expired_at');
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
        Schema::dropIfExists('jobs');
    }
};
