<?php

namespace Grrr\Pages\Resources;

use App\Nova\User;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Epartment\NovaDependencyContainer\HasDependencies;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Grrr\Pages\Filters;
use Grrr\Pages\Models\Page as PageModel;
use Gwd\SeoMeta\SeoMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Outl1ne\MultiselectField\Multiselect;
use Whitecube\NovaFlexibleContent\Flexible;

/**
 * Resource for managing pages.
 */
class PageResource extends Resource
{
    use HasDependencies;

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['createdBy', 'updatedBy', 'parent'];

    public static $search = ['title', 'url'];

    /**
     * This can be overridden by implementing the model() method, when
     * developers want to use a custom model.
     */
    public static $model = PageModel::class;

    public static $displayInNavigation = false;

    public static function label(): string
    {
        return __('pages::pages.label');
    }

    public static function singularLabel(): string
    {
        return __('pages::pages.singularLabel');
    }

    public static function uriKey()
    {
        return 'grrr-page';
    }

    /**
     * Make sure pages are ordered by url, which should show the hierarchy
     * between parents and children.
     */
    public static function indexQuery(NovaRequest $request, $query): void
    {
        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q->orderBy('url')->orderBy('title');
        });
    }

    public function filters(Request $request)
    {
        return [new Filters\Language(), new Filters\Template()];
    }

    /**
     * Extend this to provide your own flexible layouts.
     */
    public function getFlexibleLayouts(): array
    {
        return [
            [
                __('pages::pages.flexible.sample_section'),
                'content_section',
                [
                    Text::make(
                        __('pages::pages.flexible.section_title'),
                        'section_title'
                    ),
                    Textarea::make(
                        __('pages::pages.flexible.section_content'),
                        'section_content'
                    ),
                ],
            ],
        ];
    }

    /**
     * Extend to this to provide your own flexible layouts via a Preset.
     */
    public function getFlexiblePreset(): ?string
    {
        return null;
    }

    /**
     * Extend this to provide your own page statuses.
     */
    public function getPageStatusOptions(): array
    {
        return array_combine(
            PageModel::STATUSES,
            // Make sure the labels are translated.
            array_map(
                fn(string $status) => __("pages::pages.status.{$status}"),
                PageModel::STATUSES
            )
        );
    }

    /**
     * Extend this to provide your own default status.
     */
    public function getDefaultPageStatus(): string
    {
        return PageModel::STATUS_PUBLISHED;
    }

    /**
     * Extend this to use your own User resource.
     * Note that this is used to resolve "createdBy" and "updatedBy" relations
     * and probably also needs changes to the migration and model if this is
     * non-standard.
     */
    public function getUserResourceClass(): string
    {
        return User::class;
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title .
            (config('nova-pages-tool.allowTranslations')
                ? ' (' . $this->language . ')'
                : '');
    }

    public function fieldsForIndex(): array
    {
        return [
            Text::make(
                __('pages::pages.fields.title'),
                'title',
                fn() => str_repeat('-', substr_count($this->url, '/') - 1) .
                    " {$this->title}"
            ),
            Text::make(
                __('pages::pages.fields.url'),
                'url',
                fn() => $this->url
            ),
            Badge::make(
                __('pages::pages.fields.status'),
                fn() => __('pages::pages.status.' . $this->status)
            )->map([
                __('pages::pages.status.' . PageModel::STATUS_DRAFT) => 'info',
                __(
                    'pages::pages.status.' . PageModel::STATUS_PUBLISHED
                ) => 'success',
            ]),
        ];
    }

    public function fields(Request $request): array
    {
        $fields = [
            Tabs::make('', [
                Tab::make(
                    __('pages::pages.panels.basic'),
                    $this->basicFields()
                ),

                Tab::make(
                    __('pages::pages.panels.template'),
                    $this->templateFields()
                ),

                Tab::make(
                    __('pages::pages.panels.content'),
                    $this->contentFields()
                ),

                Tab::make(__('pages::pages.panels.seo'), $this->seoFields()),
            ])->withToolbar(),
        ];

        if (config('nova-pages-tool.allowTranslations')) {
            // BelongsToMany is visible on the detail view.
            $fields[] = BelongsToMany::make(
                __('pages::pages.fields.translations'),
                'translations',
                self::class
            );
        }

        return $fields;
    }

    /**
     * Used to get available translations.
     * Note that this cannot be used to switch languages at runtime in the form.
     * E.g. new pages will never be filtered by language because the
     * page is not saved yet and has no language.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Laravel\Nova\Fields\Field  $field
     */
    public static function relatablePages(NovaRequest $request, $query)
    {
        // TODO The usual order of pages is ->orderBy('url')->orderBy('title'),
        // but this field uses $this->title() to form the title, and will therefore
        // omit the dashes ("-") to show the level of nesting. This makes ordering
        // by URL really confusing.
        $query->orderBy('title');

        // The Nova MultiSelect field will go through this method, but no resource information
        // is added in the request. So this won't work with Nova MultiSelect.
        if (!$request->resourceId) {
            return $query;
        }
        $model = $request->findModelOrFail($request->resourceId);
        return $query
            ->where('id', '!=', $model->id)
            ->where('language', '!=', $model->language);
    }

    /**
     * Create an ordered set of pages, to be used in Select-type fields in Nova.
     * This can be used in userland-provided resources that need to link to
     * pages, for instance.
     *
     * @param ?string $withoutId Omit this page id from the result
     */
    public static function getPageOptionsForSelect(
        ?string $withoutId = null
    ): array {
        return once(function () use ($withoutId) {
            return PageModel::query()
                ->when(
                    $withoutId,
                    fn($builder) => $builder->where('id', '!=', $withoutId)
                )
                ->orderBy('url')
                ->orderBy('title')
                ->cursor()
                ->mapWithKeys(function (PageModel $page) {
                    $novaPage = new self($page);
                    return [
                        $page->id =>
                            str_repeat('-', substr_count($page->url, '/') - 1) .
                            " {$novaPage->title()}",
                    ];
                })
                ->toArray();
        });
    }

    protected function basicFields(): array
    {
        $basicFields = [
            Text::make(__('pages::pages.fields.title'), 'title')->rules(
                'required'
            ),
            Slug::make(__('pages::pages.fields.slug'), 'slug')
                ->from('title')
                ->default('')
                ->rules(['nullable', 'alpha_dash'])
                ->help(__('pages::pages.fields.slugHelp'))
                ->hideFromIndex(),

            Text::make(__('pages::pages.fields.url'), 'url')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                // @todo Make this configurable, because in headless CMS setups,
                // this should not link to this server.
                ->displayUsing(
                    fn(string $url) => "<a href=\"{$url}\">{$url}</a>"
                )
                ->asHtml(),

            Select::make(__('pages::pages.fields.parent'), 'parent_id')
                ->options(static::getPageOptionsForSelect())
                ->nullable()
                ->displayUsingLabels(),

            Select::make(__('pages::pages.fields.status'), 'status')
                ->options($this->getPageStatusOptions())
                ->rules('required')
                ->onlyOnForms()
                ->default($this->getDefaultPageStatus()),

            Select::make(__('pages::pages.fields.language'), 'language')
                ->required()
                ->hideFromIndex()
                ->options(config('nova-pages-tool.languages'))
                ->default(config('nova-pages-tool.defaultLanguage'))
                ->displayUsingLabels(),

            DateTime::make(__('pages::pages.fields.created_at'), 'created_at')
                ->readonly()
                ->onlyOnDetail(),

            DateTime::make(__('pages::pages.fields.updated_at'), 'updated_at')
                ->readonly()
                ->onlyOnDetail(),

            BelongsTo::make(
                __('pages::pages.fields.created_by'),
                'createdBy',
                $this->getUserResourceClass()
            )
                ->readonly()
                ->onlyOnDetail(),

            BelongsTo::make(
                __('pages::pages.fields.updated_by'),
                'updatedBy',
                User::class,
                $this->getUserResourceClass()
            )
                ->readonly()
                ->onlyOnDetail(),
        ];
        if (config('nova-pages-tool.allowTranslations')) {
            $basicFields[] = Multiselect::make(
                __('pages::pages.fields.translations'),
                'translations'
            )
                ->belongsToMany(PageResource::class)
                ->onlyOnForms();
        }
        return $basicFields;
    }

    protected function templateFields(): array
    {
        return collect([
            Select::make(__('pages::pages.fields.template'), 'template')
                ->required()
                ->hideFromIndex()
                ->options(
                    array_combine(
                        config('nova-pages-tool.templates'),
                        config('nova-pages-tool.templates')
                    )
                )
                ->default(config('nova-pages-tool.defaultTemplate')),
        ])
            ->concat($this->getTemplateDependentFields())
            ->all();
    }

    protected function contentFields(): array
    {
        $flexible = Flexible::make(__('pages::pages.fields.content'), 'content')
            ->nullable()
            ->collapsed();
        // Collect layouts and add them to the field.
        // Implementers should override getFlexibleLayouts() and provide their
        // own layouts specific to their domain.
        collect($this->getFlexibleLayouts())->each(
            fn($args) => $flexible->addLayout(...(array) $args)
        );
        // Alternatively, or additively, allow a preset to be configured for this field.
        $preset = $this->getFlexiblePreset();
        if ($preset) {
            $flexible->preset($preset);
        }
        return [$flexible];
    }

    protected function seoFields(): array
    {
        $seoFields = SeoMeta::make(__('pages::pages.fields.seo'), 'seo_meta');
        $imagesDisk = config('nova-pages-tool.seoImagesDisk');
        $seoFields->disk($imagesDisk);
        return [$seoFields];
    }

    /**
     * Validate the uniqueness of the URL, basically, which is determined by
     * combining the slug and all parent slugs.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterValidation(NovaRequest $request, $validator)
    {
        $parentId = $request->post('parent');
        $unique = Rule::unique('grrr_nova_pages', 'slug')->where(
            'parent_id',
            $parentId
        );
        if ($request->route('resourceId')) {
            $unique->ignore($request->route('resourceId'));
        }

        $uniqueValidator = Validator::make($request->only('slug'), [
            'slug' => [$unique],
        ]);

        if ($uniqueValidator->fails()) {
            $validator
                ->errors()
                ->add('slug', __('pages::pages.validation.uniqueSlug'));
        }
    }

    /**
     * Create NovaDependencyContainers for every template that offers
     * dependent fields.
     * These will become visible in the form after switching templates.
     */
    protected function getTemplateDependentFields(): array
    {
        return collect(config('nova-pages-tool.templates'))
            ->filter(
                fn(string $templateName) => method_exists(
                    $this,
                    'fieldsFor' . Str::studly($templateName)
                )
            )
            ->map(function (string $templateName) {
                $method = 'fieldsFor' . Str::studly($templateName);
                $fields = $this->$method();
                if (!is_array($fields)) {
                    throw new \Exception(
                        "Unable to deduce template-specific fields for method {$method}."
                    );
                }
                return NovaDependencyContainer::make($fields)->dependsOn(
                    'template',
                    $templateName
                );
            })
            ->all();
    }
}
