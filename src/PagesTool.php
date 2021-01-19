<?php

namespace Grrr\Pages;

use Grrr\Pages\Resources\PageResource;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class PagesTool extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('pages', __DIR__ . '/../dist/js/tool.js');
        Nova::style('pages', __DIR__ . '/../dist/css/tool.css');

        Nova::resources([PageResource::class]);
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('pages::navigation');
    }
}
