<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPageTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grrr_nova_page_translations', function (
            Blueprint $table
        ) {
            $table
                ->foreign('page_id')
                ->references('id')
                ->on('grrr_nova_pages')
                ->onDelete('cascade');
            $table
                ->foreign('translation_id')
                ->references('id')
                ->on('grrr_nova_pages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grrr_nova_page_translations', function (
            Blueprint $table
        ) {
            $table->dropForeign(['page_id']);
            $table->dropForeign(['translation_id']);
        });
    }
}
