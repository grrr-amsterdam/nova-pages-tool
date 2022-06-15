<?php

// TODO: Replace config names to lowercase, since this is the convention.
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
    'page_model_class' => \Grrr\Pages\Models\Page::class,
    'page_resource_class' => \Grrr\Pages\Resources\PageResource::class,
];
