<?php

namespace Grrr\Pages\MenuItemTypes;

use Grrr\Pages\Models\Page;
use Laravel\Nova\Fields\Select;
use OptimistDigital\MenuBuilder\MenuItemTypes\BaseMenuItemType;

/**
 * This class can be used to combine this Tool with OptimistDigital/MenuBuilder.
 * @see https://novapackages.com/packages/optimistdigital/nova-menu-builder
 */
class PageMenuItem extends BaseMenuItemType
{
    /**
     * Store of pages to aid lookup performance.
     */
    private static $allPages;

    public static function getType(): string
    {
        return 'custom';
    }

    /**
     * Get the menu link identifier that can be used to tell different custom
     * links apart (ie 'page' or 'product').
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return 'grrr-page';
    }

    /**
     * Get menu link name shown in  a dropdown in CMS when selecting link type
     * ie ('Product Link').
     *
     * @return string
     */
    public static function getName(): string
    {
        return __('pages::pages.menu_item_label');
    }

    /**
     * Get list of options shown in a select dropdown.
     *
     * Should be a map of [key => value, ...], where key is a unique identifier
     * and value is the displayed string.
     *
     * @return array
     */
    public static function getOptions(): array
    {
        return Page::query()
            ->select(['title', 'url', 'id'])
            ->orderBy('url')
            ->orderBy('title')
            ->cursor()
            ->mapWithKeys(function (Page $page) {
                return [
                    $page->id =>
                        str_repeat('-', substr_count($page->url, '/') - 1) .
                        " {$page->title}",
                ];
            })
            ->toArray();
    }

    /**
     * Get the value of the link visible to the front-end.
     *
     * Can be anything. It is up to you how you will handle parsing it.
     *
     * This will only be called when using the nova_get_menu()
     * and nova_get_menus() helpers or when you call formatForAPI()
     * on the Menu model.
     *
     * @param null $value The key from options list that was selected.
     * @param array|null $data The data from item fields.
     * @param $locale
     * @return ?string
     */
    public static function getValue(
        $value = null,
        array $data = null,
        $locale
    ): ?string {
        if (!static::$allPages) {
            static::$allPages = Page::query()->pluck('url', 'id');
        }
        return isset($data['page'])
            ? static::$allPages[$data['page']]->url ?? null
            : null;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array An array of fields.
     */
    public static function getFields(): array
    {
        return [
            Select::make(__('pages::pages.singularLabel'), 'page')
                ->required()
                // @todo Somehow pass 'locale' to getOptions(), to filter
                // irrelevant pages based on language.
                ->options(static::getOptions()),
        ];
    }
}
