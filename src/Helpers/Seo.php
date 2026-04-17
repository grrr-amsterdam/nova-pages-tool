<?php

namespace Grrr\Pages\Helpers;

use Illuminate\Support\Facades\Storage;

class Seo
{
    public static function renderAttributes(
        string $title = '',
        string $description = '',
        string $keywords = '',
        ?string $image = null,
        string $follow_type = 'index, follow'
    ): array {
        if (!$image && config('seo.default_seo_image')) {
            $image = asset(config('seo.default_seo_image'));
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'image_path' =>
                $image && strpos($image, '//') === false
                    ? Storage::url($image)
                    : $image,
            'follow_type' => $follow_type,
        ];
    }
}
