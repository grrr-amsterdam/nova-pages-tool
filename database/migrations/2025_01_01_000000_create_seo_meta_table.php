<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('seo_meta')) {
            return;
        }

        Schema::create('seo_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('keywords')->nullable();
            $table->string('follow_type')->nullable();
            $table->string('image')->nullable();
            $table->json('sociale')->nullable();
            $table->json('params')->nullable();
            $table->bigInteger('seo_metaable_id')->unsigned();
            $table->string('seo_metaable_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};
