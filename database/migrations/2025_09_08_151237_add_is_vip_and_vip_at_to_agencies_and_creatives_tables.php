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
        Schema::table('agencies', function (Blueprint $table) {
            $table->boolean('is_vip')->default(false)->after('is_featured');
            $table->timestamp('vip_at')->nullable()->after('is_vip');
        });

        Schema::table('creatives', function (Blueprint $table) {
            $table->boolean('is_vip')->default(false)->after('is_featured');
            $table->timestamp('vip_at')->nullable()->after('is_vip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('is_vip');
            $table->dropColumn('vip_at');
        });

        Schema::table('creatives', function (Blueprint $table) {
            $table->dropColumn('is_vip');
            $table->dropColumn('vip_at');
        });
    }
};
