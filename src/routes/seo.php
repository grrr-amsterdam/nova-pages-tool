<?php

use Grrr\Pages\Helpers\SeoSitemap;
use Illuminate\Support\Facades\Route;

if (config('seo.sitemap_status')) {
    Route::get(config('seo.sitemap_path'), function () {
        $sitemap = new SeoSitemap();
        return response($sitemap->toXml(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    });
}
