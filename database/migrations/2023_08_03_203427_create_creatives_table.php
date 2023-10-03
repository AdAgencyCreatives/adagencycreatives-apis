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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('years_of_experience')->nullable();
            $table->text('about')->nullable();
            $table->string('employment_type')->nullable();
            $table->text('industry_experience')->nullable();
            $table->text('media_experience')->nullable();
            $table->text('strengths')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_urgent')->default(0);
            $table->boolean('is_remote')->default(0);
            $table->boolean('is_hybrid')->default(0);
            $table->boolean('is_onsite')->default(0);
            $table->boolean('is_opentorelocation')->default(0);

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
