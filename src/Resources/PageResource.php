<?php

namespace Grrr\Pages\Resources;

use App\Nova\User;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\TabsOnEdit;
use Epartment\NovaDependencyContainer\HasDependencies;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Grrr\Pages\Filters\Language;
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
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use NovaAttachMany\AttachMany;
use Whitecube\NovaFlexibleContent\Flexible;

/**
 * Resource for managing pages.
 */
class PageResource extends Resource
{
    use HasDependencies;
    use TabsOnEdit;

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
        return [new Language()];
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
     * Extend this to provide your own template options.
     */
    public function getPageTemplates(): array
    {
        $options = [PageModel::TEMPLATE_DEFAULT];
        return array_combine($options, $options);
    }

    /**
     * Extend this to provide your own default template.
     */
    public function getDefaultPageTemplate(): string
    {
        return PageModel::TEMPLATE_DEFAULT;
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
        return $this->title . ' (' . $this->language . ')';
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

        return [
            Tabs::make('', [
                Tab::make(__('pages::pages.panels.basic'), [
                    Text::make(__('pages::pages.fields.title'), 'title')->rules(
                        'required'
                    ),

                    Slug::make(__('pages::pages.fields.slug'), 'slug')
                        ->from('title')
                        ->default('')
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
                        ->options(
                            self::getPageOptionsForSelect(
                                $this->model()->getKey()
                            )
                        )
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

                    DateTime::make(
                        __('pages::pages.fields.created_at'),
                        'created_at'
                    )
                        ->readonly()
                        ->onlyOnDetail(),

                    DateTime::make(
                        __('pages::pages.fields.updated_at'),
                        'updated_at'
                    )
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

                    // AttachMany is visible in the form.
                    AttachMany::make(
                        __('pages::pages.fields.translations'),
                        'translations',
                        self::class
                    ),

                    // BelongsToMany is visible on the detail view.
                    BelongsToMany::make(
                        __('pages::pages.fields.translations'),
                        'translations',
                        self::class
                    ),
                ]),
                Tab::make(
                    __('pages::pages.panels.template'),
                    collect([
                        Select::make(
                            __('pages::pages.fields.template'),
                            'template'
                        )
                            ->required()
                            ->hideFromIndex()
                            ->options($this->getPageTemplates())
                            ->default($this->getDefaultPageTemplate()),
                    ])
                        ->concat($this->getTemplateDependentFields())
                        ->all()
                ),

                Tab::make(__('pages::pages.panels.content'), [$flexible]),

                Tab::make(__('pages::pages.panels.seo'), [
                    SeoMeta::make(__('pages::pages.fields.seo'), 'seo_meta'),
                ]),
            ])->withToolbar(),
        ];
    }

    /**
     * Used to get translations.
     */
    public static function relatablePages(
        NovaRequest $request,
        Builder $query,
        Field $field
    ): Builder {
        // @todo How can we test this?
        if (!$request->resourceId) {
            return $query;
        }
        $model = $request->findModelOrFail($request->resourceId);
        $query->where('id', '!=', $model->id);

        if ($field->attribute === 'translations') {
            $query->where('language', '!=', $model->language);
        }

        return $query;
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
        return PageModel::query()
            ->select(['title', 'url', 'id'])
            ->when(
                $withoutId,
                fn($builder) => $builder->where('id', '!=', $withoutId)
            )
            ->orderBy('url')
            ->orderBy('title')
            ->cursor()
            ->mapWithKeys(function (PageModel $page) {
                return [
                    $page->id =>
                        str_repeat('-', substr_count($page->url, '/') - 1) .
                        " {$page->title}",
                ];
            })
            ->toArray();
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

    protected function getTemplateDependentFields(): array
    {
        return collect($this->getPageTemplates())
            ->filter(
                fn(string $templateName) => method_exists(
                    $this,
                    "fieldsFor" . Str::studly($templateName)
                )
            )
            ->map(function (string $templateName) {
                $method = "fieldsFor" . Str::studly($templateName);
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
