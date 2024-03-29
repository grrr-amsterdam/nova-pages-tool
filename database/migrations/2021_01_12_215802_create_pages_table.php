<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('grrr_nova_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table
                ->string('url')
                ->nullable()
                ->index();
            $table->text('content')->nullable();
            $table->string('template')->default('default');
            $table->string('status')->default('PUBLISHED');
            $table->string('language');
            $table->timestamps();

            // Columns as used by axn/laravel-eloquent-authorable
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('users');

            $table
                ->foreign('updated_by')
                ->references('id')
                ->on('users');
        });

        // Adding a foreign key to the same table while creating it is not possible.
        Schema::table('grrr_nova_pages', function (Blueprint $table) {
            $table
                ->foreignId('parent_id')
                ->nullable()
                ->constrained('grrr_nova_pages');

            // Create compound unique index on parent_id and slug: slugs can
            // only exist once at the same level.
            $table->unique(['parent_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('grrr_nova_pages');
    }
}
