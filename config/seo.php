<?php

return [
    'seo_status' => env('SEO_STATUS', true),
    'sitemap_status' => env('SITEMAP_STATUS', false),
    'title_formatter' => ':text',
    'follow_type_options' => [
        'index, follow' => 'Index and follow',
        'noindex, follow' => 'No index and follow',
        'index, nofollow' => 'Index and no follow',
        'noindex, nofollow' => 'No index and no follow',
    ],
    'default_follow_type' => env('SEO_DEFAULT_FOLLOW_TYPE', 'index, follow'),
    'default_seo_image' => null,
    'sitemap_models' => [],
    'sitemap_path' => '/sitemap',
    'disk' => env('SEO_IMAGE_DISK', 'public'),
];
