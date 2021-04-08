<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nova Pages module
    |--------------------------------------------------------------------------
    |
    */

    'label' => 'Pagina\'s',
    'singularLabel' => 'Pagina',
    'fields' => [
        'title' => 'Titel',
        'url' => 'Url',
        'status' => 'Status',
        'content' => 'Inhoud',
        'slug' => 'Slug',
        'slugHelp' => 'Samen met de slugs van bovenliggende pagina\'s vormt dit de URL van de pagina. Het leeglaten van de slug is alleen toegestaan voor de homepage, omdat dit een URL van "/" veroorzaakt.
         <br>⚠️  Let op: het updaten van de slug past de URL aan van alle onderliggende pagina\'s!',
        'template' => 'Template',
        'parent' => 'Bovenliggende pagina',
        'language' => 'Taal',
        'created_at' => 'Aangemaakt op',
        'updated_at' => 'Aangepast op',
        'created_by' => 'Aangemaakt door',
        'updated_by' => 'Aangepast door',
    ],
    'panels' => [
        'basic' => 'Basisinformatie',
        'content' => 'Inhoud',
        'meta' => 'Meta informatie',
    ],
    'status' => [
        'PUBLISHED' => 'Gepubliceerd',
        'DRAFT' => 'Klad',
    ],
    'flexible' => [
        'sample_section' => 'Sectie',
        'section_title' => 'Sectie titel',
        'section_content' => 'Sectie inhoud',
    ],
    'menu_item_label' => 'Pagina link',
    'validation' => [
        'uniqueSlug' =>
            'De slug is niet uniek — hij bestaat al op dit niveau. Kies een andere slug, of een andere bovenliggende pagina.',
    ],
];
