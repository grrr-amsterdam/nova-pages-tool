<?php namespace Grrr\Pages\Models;

use Grrr\Pages\Events\AttachedTranslation;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PageTranslation extends Pivot
{
    protected $table = 'grrr_nova_page_translation';

    protected $dispatchesEvents = [
        'created' => AttachedTranslation::class,
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function translation()
    {
        return $this->belongsTo(Page::class);
    }
}
