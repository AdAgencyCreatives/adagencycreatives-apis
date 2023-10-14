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
        Schema::create('creatives', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('title')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->string('years_of_experience')->nullable()->index();
            $table->text('about')->nullable();
            $table->string('employment_type')->nullable()->index();
            $table->text('industry_experience')->nullable();
            $table->text('media_experience')->nullable();
            $table->string('strengths', 505)->nullable()->index();
            $table->boolean('is_featured')->default(0)->index();
            $table->boolean('is_urgent')->default(0)->index();
            $table->boolean('is_remote')->default(0)->index();
            $table->boolean('is_hybrid')->default(0)->index();
            $table->boolean('is_onsite')->default(0)->index();
            $table->boolean('is_opentorelocation')->default(0)->index();

            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
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
        Schema::dropIfExists('creatives');
    }
};
