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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->morphs('bookmarkable');
            // $table->string('bookmarkable_type'); // Polymorphic type to indicate the type of entity being bookmarked (e.g., Agency, Creative, JobPost, etc.)
            // $table->unsignedBigInteger('bookmarkable_id'); // Polymorphic ID to store the ID of the specific entity being bookmarked
            $table->timestamps();
            $table->softDeletes();

            // $table->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id']); // Ensure a user can only bookmark an entity once
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookmarks');
    }
};
