<?php

return [
    /**
     * Use an empty array for unilingual websites.
     */
    'languages' => [
        'nl' => 'Nederlands',
        'en' => 'English',
    ],
    'defaultLanguage' => 'nl',

    'templates' => [\Grrr\Pages\Models\Page::TEMPLATE_DEFAULT],
    'defaultTemplate' => \Grrr\Pages\Models\Page::TEMPLATE_DEFAULT,
];
