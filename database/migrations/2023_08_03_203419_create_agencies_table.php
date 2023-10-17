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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('slug')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('size')->nullable()->index();
            $table->text('about')->nullable();
            $table->string('industry_experience', 505)->nullable()->index();
            $table->string('media_experience', 505)->nullable()->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_urgent')->default(0)->index();
            $table->boolean('is_remote')->default(0)->index();
            $table->boolean('is_hybrid')->default(0)->index();
            $table->boolean('is_onsite')->default(0)->index();

            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->integer('views')->default(0);

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
        Schema::dropIfExists('agencies');
    }
};
