<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('package_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->string('start_date')->default('ASAP')->comment('ASAP', 'Tomorrow', 'In one week', 'In two weeks');
            $table->string('employment_type')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('salary_range')->nullable();
            $table->text('industry_experience')->nullable();
            $table->text('media_experience')->nullable();
            $table->boolean('is_opentorelocation')->default(0);
            $table->boolean('is_opentoremote')->default(0);
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('package')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_requests');
    }
};