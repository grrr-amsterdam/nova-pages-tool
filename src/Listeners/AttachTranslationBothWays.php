<?php

namespace Grrr\Pages\Listeners;

use Grrr\Pages\Events\AttachedTranslation;
use Grrr\Pages\Models\PageTranslation;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ErrorHandler\Debug;

class AttachTranslationBothWays
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
