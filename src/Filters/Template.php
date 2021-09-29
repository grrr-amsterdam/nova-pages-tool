<?php

namespace Grrr\Pages\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class Template extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('template', $value);
    }

    /**
     * The default  value of the filter
     *
     * @return string
     */
    public function default(): ?string
    {
        return null;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request): array
    {
        return array_combine(
            config('nova-pages-tool.templates'),
            config('nova-pages-tool.templates')
        );
    }
}
