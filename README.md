# Nova Page module

![CI](https://github.com/grrr-amsterdam/nova-pages-tool/workflows/CI/badge.svg)

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

And register the tool in your `NovaServiceProvider`:

```php
use use Grrr\Pages\PagesTool;

public function tools(): array
{
    return [new PagesTool()];
}
```

## Usage and extension

Out of the box you will get a Pages tool in your Nova back-end to manage pages. If everything is to your liking, you can just start using it as-is!

However, you will probably want to add some layouts to be used in the flexible-content portion of the page.
You can create your own `Page` resource and extend `Grrr\Pages\Resources\PageResource`. Override the `getFlexibleLayouts()` method to specify your own flexible content layouts:

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

### Other extensions

A full list of methods that you are likely to want to customize is the following:

- `getFlexibleLayouts()`: to determine which layouts to support in the flexible content.
- `getPageTemplates()`: to determine which templates to support as page property.
- `getDefaultPageTemplate()`: to determine which template should be the default.
- `getPageStatusOptions()`: to determine the default status of a page.
- `getLanguageOptions()`: to determine which languages to support in your pages.
- `getDefaultLanguage()`: defaults to `config('app.locale')`.

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
- [Axn/laravel-eloquent-authorable](https://github.com/AXN-Informatique/laravel-eloquent-authorable)
- [OptimistDigital/MenuBuilder](https://github.com/optimistdigital/nova-menu-builder)
