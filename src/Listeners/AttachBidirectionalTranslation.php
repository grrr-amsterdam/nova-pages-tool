<?php

namespace Grrr\Pages\Listeners;

use Grrr\Pages\Events\AttachedTranslation;
use Grrr\Pages\Models\PageTranslation;

class AttachBidirectionalTranslation
{
    public function __construct()
    {
    }

    public function handle(AttachedTranslation $event): void
    {
        $translation = $event->translation;

        $page = $translation->page;
        $translation = $translation->translation;

        $translation = new PageTranslation([
            'page_id' => $translation->id,
            'translation_id' => $page->id,
        ]);
        $translation->saveQuietly();
    }
}
