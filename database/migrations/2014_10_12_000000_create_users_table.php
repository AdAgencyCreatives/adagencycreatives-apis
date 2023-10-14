<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('username')->unique()->index();
            $table->string('email')->unique()->index();
            $table->string('password');
            $table->integer('role')->comment('1:admin, 2:advisory, 3:agency, 4:creative')->index();
            $table->integer('status')->default(0)->comment('0:pending, 1:active, 2:inactive')->index();
            $table->boolean('is_visible')->default(true);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
