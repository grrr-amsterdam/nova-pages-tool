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
];
