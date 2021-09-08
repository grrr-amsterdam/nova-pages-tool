<?php

namespace Grrr\Pages\Models;

use Axn\EloquentAuthorable\AuthorableTrait;
use Grrr\Pages\Database\Factories\PageFactory;
use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Events\SavedPage;
use Gwd\SeoMeta\Traits\SeoMetaTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Nova\Actions\Actionable;
use Whitecube\NovaFlexibleContent\Value\FlexibleCast;

/**
 * @property Collection $children
 * @property string $content
 * @property int $id
 * @property string $language
 * @property Page $parent
 * @property string $slug
 * @property string $status
 * @property string $template
 * @property string $title
 * @property string $url
 */
class Page extends Model
{
    use HasFactory;
    use SeoMetaTrait;
    use AuthorableTrait;
    use Actionable;

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
    ];

    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}
