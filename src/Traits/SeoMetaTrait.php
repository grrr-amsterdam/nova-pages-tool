<?php

namespace Grrr\Pages\Traits;

use Grrr\Pages\Models\SeoMetaItem;
use Illuminate\Support\Facades\Storage;

trait SeoMetaTrait
{
    public function seo_meta()
    {
        return $this->morphOne(SeoMetaItem::class, 'seo_metaable');
    }

    public function getSeoMeta()
    {
        $attrs = false;

        if ($this->seo_meta) {
            $attrs = $this->seo_meta->toArray();
        } else {
            $title = $this->getSeoTitleDefault();

            if ($title) {
                $formatter =
                    $this->getSeoTitleFormatter() ??
                    config('seo.title_formatter');
                $attrs = [
                    'title' => $title,
                    'description' => $this->getSeoDescriptionDefault(),
                    'keywords' => $this->getSeoKeywordsDefault(),
                    'image' => $this->getSeoImageDefault(),
                    'follow_type' => $this->getSeoFollowDefault(),
                    'params' => (object) [
                        'title_format' => $formatter,
                    ],
                ];
            }
        }

        if ($attrs && isset($attrs['image']) && $attrs['image']) {
            $attrs['image_path'] =
                strpos($attrs['image'], '//') === false
                    ? Storage::disk(config('seo.disk'))->url($attrs['image'])
                    : $attrs['image'];
        }

        return $attrs;
    }

    public function getSeoTitleFormatter()
    {
        return config('seo.title_formatter');
    }

    public function getSeoTitleDefault()
    {
        return null;
    }

    public function getSeoDescriptionDefault()
    {
        return null;
    }

    public function getSeoKeywordsDefault()
    {
        return null;
    }

    public function getSeoImageDefault()
    {
        if (config('seo.default_seo_image')) {
            return asset(config('seo.default_seo_image'));
        }
        return null;
    }

    public function getSeoFollowDefault()
    {
        return config('seo.default_follow_type');
    }

    public function getCanonicalLinks(): array
    {
        return $this->seo_meta?->params?->canonical_links ?? [];
    }
}
