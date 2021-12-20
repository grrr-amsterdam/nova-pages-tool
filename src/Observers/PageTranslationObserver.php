<?php

namespace Grrr\Pages\Observers;

use Grrr\Pages\Models\PageTranslation;

class PageTranslationObserver
{
    /**
     * Handle the PageTranslation "created" event.
     *
     * @param  PageTranslation  $pageTranslation
     * @return void
     */
    public function created(PageTranslation $translation)
    {
        $page = $translation->page;
        $translation = $translation->translation;

        $translation = new PageTranslation([
            'page_id' => $translation->id,
            'translation_id' => $page->id,
        ]);
        $translation->saveQuietly();
    }

    /**
     * Handle the PageTranslation "updated" event.
     *
     * @param  PageTranslation  $pageTranslation
     * @return void
     */
    public function updated(PageTranslation $pageTranslation)
    {
    }

    /**
     * Handle the PageTranslation "deleted" event.
     *
     * @param  PageTranslation  $pageTranslation
     * @return void
     */
    public function deleted(PageTranslation $pageTranslation)
    {
        // Remove translation from page
        PageTranslation::withoutEvents(function () use ($pageTranslation) {
            $page = $pageTranslation->page;
            $translation = $pageTranslation->translation;

            $translation->translations()->detach($page->id);
        });
    }

    /**
     * Handle the PageTranslation "restored" event.
     *
     * @param  PageTranslation  $pageTranslation
     * @return void
     */
    public function restored(PageTranslation $pageTranslation)
    {
        //
    }

    /**
     * Handle the PageTranslation "force deleted" event.
     *
     * @param  PageTranslation  $pageTranslation
     * @return void
     */
    public function forceDeleted(PageTranslation $pageTranslation)
    {
        //
    }
}
