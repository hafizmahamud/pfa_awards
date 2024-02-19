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
        Schema::table('document_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('title');
            $table->foreign('category_id')
                ->references('id')
                ->on('document_collection_categories')
                ->onDelete('set null');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_collections', function (Blueprint $table) {
            $table->dropForeign('document_collections_category_id_foreign');
            $table->dropColumn('category_id');
        });
    }
};




