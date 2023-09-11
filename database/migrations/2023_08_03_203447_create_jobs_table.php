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
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('category_id');
            $table->string('agency_name')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('employement_type');
            $table->text('industry_experience');
            $table->text('media_experience');
            $table->string('salary_range');
            $table->string('years_of_experience');
            $table->string('apply_type');
            $table->string('external_link')->nullable();
            $table->integer('status')->comment('0:pending, 1:approved, 2:rejected, 3:expired, 4:filled, 5:draft, 6:published');
            $table->boolean('is_remote')->default(0);
            $table->boolean('is_hybrid')->default(0);
            $table->boolean('is_onsite')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_urgent')->default(0);
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
