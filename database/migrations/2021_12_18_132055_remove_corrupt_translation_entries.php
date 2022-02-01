<?php

use Grrr\Pages\Models\Page;
use Grrr\Pages\Models\PageTranslation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCorruptTranslationEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove translations that have no page or translation id
        // or where the page or translation id do not exist in te pages table
        $existingPageIds = Page::all()->pluck('id');
        PageTranslation::whereNull('page_id')
            ->orWhereNull('translation_id')
            ->orWhereNotIn('page_id', $existingPageIds)
            ->orWhereNotIn('translation_id', $existingPageIds)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
