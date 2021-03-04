<?php namespace Grrr\Pages\Events;

use Grrr\Pages\Models\PageTranslation;

class AttachedTranslation
{
    public $translation;

    public function __construct(PageTranslation $translation)
    {
        $this->translation = $translation;
    }
}
