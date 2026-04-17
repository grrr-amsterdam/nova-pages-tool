<?php

namespace Grrr\Pages\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMetaItem extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['seo_metaable_type', 'created_at', 'updated_at'];
    protected $table = 'seo_meta';
    protected $casts = ['params' => 'object'];

    public function seo_metaable()
    {
        return $this->morphTo();
    }
}
