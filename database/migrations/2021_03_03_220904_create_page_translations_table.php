<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grrr_nova_page_translation', function (
            Blueprint $table
        ) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('translation_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grrr_nova_page_translation');
    }
}
