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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone')->nullable()->after('email');
            $table->string('mobile')->nullable()->after('telephone');
            $table->string('jurisdiction')->nullable()->after('mobile');
            $table->string('role')->nullable()->after('jurisdiction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telephone');
            $table->dropColumn('mobile');
            $table->dropColumn('jurisdiction');
            $table->dropColumn('role');
        });
    }
};
