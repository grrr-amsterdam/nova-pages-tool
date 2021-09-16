<?php

namespace Grrr\Pages\Nova\Flexible\Layouts;

use Grrr\Pages\Resources\PageResource;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use NumaxLab\NovaCKEditor5Classic\CKEditor5Classic;
use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class Collection extends Layout
{
    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'collection';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Collection';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array<mixed>
     */
    public function fields(): array
    {
        return [
            Text::make('foo'),
            Select::make(
                __('pages::pages.flexible.collection.page_template'),
                'page_template'
            )
                ->rules('required')
                ->options($this->getPageTemplates()),
        ];
    }

    /**
     * @return array<string>
     */
    public function getPageTemplates(): array
    {
        return Nova::resourceInstanceForKey('grrr-page')->getPageTemplates();
    }
}
