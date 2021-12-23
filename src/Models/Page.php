<?php

namespace Grrr\Pages\Models;

use Axn\EloquentAuthorable\AuthorableTrait;
use Grrr\Pages\Database\Factories\PageFactory;
use Grrr\Pages\Events\DeletedPage;
use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Events\SavedPage;
use Gwd\SeoMeta\Models\SeoMetaItem;
use Gwd\SeoMeta\Traits\SeoMetaTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Nova\Actions\Actionable;
use Whitecube\NovaFlexibleContent\Concerns\HasFlexible;
use Whitecube\NovaFlexibleContent\Value\FlexibleCast;

/**
 * @property Collection $children
 * @property Collection $content
 * @property array $metadata
 * @property int $id
 * @property string $language
 * @property Page $parent
 * @property string $slug
 * @property string $status
 * @property string $template
 * @property string $title
 * @property string $url
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property SeoMetaItem $seo_meta
 */
class Page extends Model
{
    use Actionable;
    use AuthorableTrait;
    use HasFactory;
    use HasFlexible;
    use SeoMetaTrait;

    const STATUS_PUBLISHED = 'PUBLISHED';
    const STATUS_DRAFT = 'DRAFT';

    const STATUSES = [self::STATUS_PUBLISHED, self::STATUS_DRAFT];

    const TEMPLATE_DEFAULT = 'default';

    /**
     * @todo Make configurable.
     */
    protected $table = 'grrr_nova_pages';

    protected $casts = [
        'content' => FlexibleCast::class,
        'metadata' => 'array',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => SavingPage::class,
        'saved' => SavedPage::class,
        'deleted' => DeletedPage::class,
    ];

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function translations(): BelongsToMany
    {
        return $this->belongsToMany(
            static::class,
            'grrr_nova_page_translations',
            'page_id',
            'translation_id'
        )
            ->using(PageTranslation::class)
            ->withTimestamps();
    }

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}
