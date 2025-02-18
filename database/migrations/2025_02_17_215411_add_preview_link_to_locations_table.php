<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('preview_link')->nullable()->after('parent_id');
            $table->unsignedInteger('sort_order')->after('preview_link')->default(11);
            $table->boolean('is_featured')->after('sort_order')->default(0);
        });
    }


    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('preview_link');
            $table->dropColumn('is_featured');
            $table->dropColumn('sort_order');
        });
    }
};
