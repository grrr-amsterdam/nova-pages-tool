<?php

use Grrr\Pages\Models\Page as PageModel;

return [
    'languages' => [
        'nl' => 'Nederlands',
        'en' => 'English',
    ],
    'defaultLanguage' => 'nl',

    // Disable for unilingual websites.
    'allowTranslations' => true,

    'templates' => [PageModel::TEMPLATE_DEFAULT],
    'defaultTemplate' => PageModel::TEMPLATE_DEFAULT,
];
