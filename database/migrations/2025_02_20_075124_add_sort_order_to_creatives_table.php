<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('creatives', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->after('welcomed_at')->default(100);
        });
    }

    public function down()
    {
        Schema::table('creatives', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
