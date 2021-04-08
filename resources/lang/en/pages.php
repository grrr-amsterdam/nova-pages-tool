<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nova Pages module
    |--------------------------------------------------------------------------
    |
    */

    'label' => 'Pages',
    'singularLabel' => 'Page',
    'fields' => [
        'title' => 'Title',
        'url' => 'Url',
        'status' => 'Status',
        'content' => 'Content',
        'slug' => 'Slug',
        'slugHelp' =>
            'Together with the slugs of the parent page(s), this will form the URL of this page. Leaving this empty is only allowed for the homepage, since the URL will be "/". <br>⚠️  Note: updating the slug will change the URL and that of all child pages!',
        'template' => 'Template',
        'parent' => 'Parent page',
        'language' => 'Language',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
        'created_by' => 'Created by',
        'updated_by' => 'Updated by',
    ],
    'panels' => [
        'basic' => 'Basic information',
        'content' => 'Content',
        'meta' => 'Meta information',
    ],
    'status' => [
        'PUBLISHED' => 'Published',
        'DRAFT' => 'Draft',
    ],
    'flexible' => [
        'sample_section' => 'Sample section',
        'section_title' => 'Section title',
        'section_content' => 'Section content',
    ],
    'menu_item_label' => 'Page link',
    'validation' => [
        'uniqueSlug' =>
            'This slug is not unique — it already exists at this level. Choose another slug, or change to another parent page.',
    ],
];
