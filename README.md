# Nova Page module

This tool allows you to manage pages in your app.

It will take care of URL generation and contains some default fields for pages.

You can easily extend this package's resource to modify its behavior.

## Installation

```
composer require grrr-amsterdam/nova-pages-tool
```

After installation, run the migrations:

```
php artisan migrate
```

## Usage and extension

Out of the box you will get a Pages tool in your Nova back-end to manage pages. If everything is to your liking, you can just start using it as-is!

However, you will probably want to add some layouts to be used in the flexible-content portion of the page.
Extend `Grrr\Pages\Resources\PageResource`, and override the `getFlexibleLayouts()` method:

```php
namespace App\Nova;

class Page extends \Grrr\Pages\Resources\PageResource
{
  public function getFlexibleLayouts(): array
  {
    return [
      [
        'Simple content section',
        'wysiwyg',
        [Text::make('Title'), Markdown::make('Content')],
      ],
      MySimpleWysiwygLayout::class,
    ];
  }
}
```

As you can see, both array-notation and custom layout classes are supported. Anything you can pass to `Flexible::make()->addLayout()` will be a valid entry in the array.

### Other extensions

A full list of methods that you are likely to want to customize is the following:

- `getFlexibleLayouts()`: to determine which layouts to support in the flexible content.
- `getPageTemplates()`: to determine which templates to support as page property.
- `getDefaultPageTemplate()`: to determine which template should be the default.
- `getPageStatusOptions()`: to determine the default status of a page.

#### Using your own model

You can override the `model()` method of the resource to return the class to your own `Page` model:

```php
public function model(): string
{
    return \App\Models\Page::class;
}
```

#### Using a custom User resource

This Tool assumes you're using `App\Nova\User` as the User resource (used to store author information).  
If you're not, override the `getUserResourceClass()` method:

```php
public function getUserResourceClass(): string
{
    return \My\User\Resource::class;
}
```

## Rendering pages in the front-end

At the time of writing this is not yet finished.  
At GRRR we like to use headless CMSes, so the actual rendering is not a responsibility of the Laravel framework in our case.

How we would implement this:

- Implement a catch-all route or middleware.
- See if the request matches the `url` property of a page in the database.
- If so, render the template as stored in the page's `template` property.

## Credits

This plugin utilizes the following packages:

- [Whitecube/NovaFlexibleContent](https://github.com/whitecube/nova-flexible-content)
- [AndreasGJ/seo-meta-field-nova](https://github.com/AndreasGJ/seo-meta-field-nova)
- [Axn/laravel-eloquent-authorable](https://github.com/AXN-Informatique/laravel-eloquent-authorable)
