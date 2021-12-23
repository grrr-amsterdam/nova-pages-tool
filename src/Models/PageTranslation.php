<?php namespace Grrr\Pages\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PageTranslation extends Pivot
{
    protected $table = 'grrr_nova_page_translations';

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function translation()
    {
        return $this->belongsTo(Page::class);
    }
}
