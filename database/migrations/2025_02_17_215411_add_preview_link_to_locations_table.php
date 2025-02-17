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
            $table->string('is_featured')->after('preview_link')->default(0);
        });
    }


    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('preview_link');
            $table->dropColumn('is_featured');
        });
    }
};
