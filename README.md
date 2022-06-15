![CI](https://github.com/grrr-amsterdam/nova-pages-tool/workflows/CI/badge.svg)

# Nova Page module

This tool allows you to manage pages in your app.

It will take care of URL generation and contains some default fields for pages.

You can easily extend this package's resource to modify its behavior.

## Developed with ❤️ by [GRRR](https://grrr.nl)

- GRRR is a [B Corp](https://grrr.nl/en/b-corp/)
- GRRR has a [tech blog](https://grrr.tech/)
- GRRR is [hiring](https://grrr.nl/en/jobs/)
- [@GRRRTech](https://twitter.com/grrrtech) tweets

## Installation

```
composer require grrr-amsterdam/nova-pages-tool
```

After installation, run the migrations:

```
php artisan migrate
```

And register the tool in your `NovaServiceProvider`:

```php
use use Grrr\Pages\PagesTool;

public function tools(): array
{
    return [new PagesTool()];
}
```

You can publish the config file to customize the tool:

```
php artisan vendor:publish --provider="Grrr\Pages\ToolServiceProvider" --tag="config"
```

## Usage and extension

You can create your own `Page` resource and extend `Grrr\Pages\Resources\PageResource`. By default
the `Grrr\Pages\Models\Page` model is registered. You can overwrite the model for the page resource
by

Override the `getFlexibleLayouts()` method to specify your own flexible content layouts:

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
[See the FlexibleContent documentation](https://whitecube.github.io/nova-flexible-content/#/?id=adding-layouts) for details.

#### Using presets

Alternatively, you can bundle your layouts in a [Preset class](https://whitecube.github.io/nova-flexible-content/#/?id=predefined-preset-classes).

Extend `getFlexiblePreset()` to provide its classname:

```php
public function getFlexiblePreset(): ?string
{
  return \App\Nova\Flexible\Presets\MyPreset::class;
}
```

### Custom fields per template

You might want to provide your users with different fields based on the chosen template.  
For instance, let's say you provide 3 templates: `default`, `homepage` and `news-article`.

The `homepage` features a large header with hero image, whereas the `news-article` page should have an introduction field and publication date.

By implementing `fieldsForHomepage` and `fieldsForNewsArticle`, you can provide these fields. In the front-end they're made conditionally available based on the chosen template:

```php
public function fieldsForHomepage(): array
{
  return [
    Image::make('Hero image', 'metadata->hero_image')
  ];
}

public function fieldsForNewsArticle(): array
{
  return [
    Textarea::make('Introduction', 'metadata->introduction'),
    Date::make('Publication date', 'metadata->publication_date')
  ];
}
```

Any method in the form `fieldsFor<template>` will be automatically picked up.

### Storing metadata

Notice how in the above example we're using this notation for the dynamic fields: `metadata->introduction`.  
This works because we provide a `metadata` json field for the Page model. Using a field like this will save the value as a property of that JSON field.

This allows you to store arbitrary data with your pages without having to extend it with your own migrations (although you totally can of course!).

### Configuration

If you've published the configuration, you can customize settings in `config/nova-pages-tool.php`.  
The following values are available:

- `languages`: which languages are available in your application.
- `defaultLanguage`: the default language.
- `allowTranslations`: whether to enable the Translations widget in the edit form. Unilingual websites generally don't need this.
- `templates`: which templates are available for your pages.
- `defaultTemplate`: the default template.

### Other extensions

A full list of methods that you are likely to want to customize is the following:

- `getFlexibleLayouts()`: to determine which layouts to support in the flexible content.
- `getPageStatusOptions()`: to determine the default status of a page.

#### Using a custom User resource

This Tool assumes you're using `App\Nova\User` as the User resource (used to store author information).  
If you're not, override the `getUserResourceClass()` method:

```php
public function getUserResourceClass(): string
{
    return \My\User\Resource::class;
}
```

## Rendering a Page selector elsewhere in your CMS

It makes sense to want to create a `Page` selector on other resources, when you want other models to link to pages for instance.  
The `PageResource` class provides a convenience method for rendering the options for such a field, neatly ordered and indented based on their level of nesting.

Use it like this:

```php
Select::make('My page')->options(PageResource::getPageOptionsForSelect());
```

## Integration with OptimistDigital/MenuBuilder

You can use this tool together with [OptimistDigital/MenuBuilder](https://github.com/optimistdigital/nova-menu-builder).  
Follow their documentation, and then add the following custom menu type to the MenuBuilder configuration:

```php
    'menu_item_types' => [\Grrr\Pages\MenuItemTypes\PageMenuItem::class],
```

This will allow admins to choose a page from this Tool as a menu item.

## Multilingual support

The way we offer multilingual support is by relating pages in differing languages together.  
A page belongsToMany other pages, and that way you can offer a translation.

We've tried various plugins for offering side-by-side translation in the same form (usually based on Spatie's Translatable package), but none of these proved without fault when combined with other packages like FlexibleContent.

So this is a very lightweight, simple approach to multilinguality, but it can support large content sites.

## Rendering pages in the front-end

At the time of writing this is not yet finished.  
At GRRR we like to use headless CMSes, so the actual rendering is not a responsibility of the Laravel framework in our case.

How we would implement this:

- Implement a catch-all route or middleware.
- See if the request matches the `url` property of a page in the database.
- If so, render the template as stored in the page's `template` property.

## Contributing

Contributions are always welcome!

You can fork this repo, run `composer install` and `npm install` and start hacking away.

### Setup testing environment

In this repo we have a `phpunit.xml.dist` file with environment variables set up for our CI flow. This can be overwritten by you own `phpunit.xml` file.

### Code quality assurance

Please make sure the tests pass, we use those to ensure everything keeps working.

Next to unit tests, we do a couple of small validations to ensure everything looks consistent.  
Your Pull Request will be validated automatically through Github Actions.

1. We validate commit messages. [Take a look at the documentation of this Github Action to make sure your commit messages validate](https://github.com/harmenjanssen/commit-message-validation-action).
2. We also use [Prettier](https://prettier.io/) to automatically format our code. You can run `composer format` to format your changes before committing.

Thanks! 🌸

## Credits

This plugin utilizes the following packages:

- [Whitecube/NovaFlexibleContent](https://github.com/whitecube/nova-flexible-content)
- [AndreasGJ/seo-meta-field-nova](https://github.com/AndreasGJ/seo-meta-field-nova)
- [Axn/laravel-eloquent-authorable](https://github.com/AXN-Informatique/laravel-eloquent-authorable)
- [OptimistDigital/MenuBuilder](https://github.com/optimistdigital/nova-menu-builder)
- [Epartment/NovaDependencyContainer](https://github.com/epartment/nova-dependency-container)
- [Eminiarts/nova-tabs](https://github.com/eminiarts/nova-tabs)
