<?php

namespace Grrr\Pages;

use Grrr\Pages\Resources\PageResource;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
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
        Nova::resources([PageResource::class]);
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make("Pages")
            ->canSee(function ($request) {
                return PageResource::authorizedToViewAny($request);
            })
            ->path("/resources/grrr-page")
            ->icon("document-text");
    }

    // /**
    //  * Build the view that renders the navigation links for the tool.
    //  *
    //  * @return \Illuminate\View\View
    //  */
    // public function renderNavigation()
    // {
    //     return view('pages::navigation');
    // }
}
