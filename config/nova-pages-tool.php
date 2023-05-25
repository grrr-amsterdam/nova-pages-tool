<?php

return [
    'languages' => [
        'nl' => 'Nederlands',
        'en' => 'English',
    ],
    'defaultLanguage' => 'nl',

    // Disable for unilingual websites.
    'allowTranslations' => true,

    'templates' => [\Grrr\Pages\Models\Page::TEMPLATE_DEFAULT],
    'defaultTemplate' => \Grrr\Pages\Models\Page::TEMPLATE_DEFAULT,

    'seoImagesDisk' => env('SEO__DISK', 'public'),

    // The URL to the front-end. Use this when you're using Nova as a headless
    // CMS and the links from the CMS to the front-end should go to another
    // host.
    'frontendUrl' => env('FRONTEND_URL', ''),
];
