<?php

namespace Tests\Unit\Fields;

use Grrr\Pages\Fields\SeoMeta;
use Grrr\Pages\Models\Page;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Http\Requests\NovaRequest;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class SeoMetaTest extends TestCase
{
    #[Test]
    public function follow_type_is_stored_as_plain_text(): void
    {
        $page = $this->createPage();

        $this->fillSeoMeta($page, ['follow_type' => 'noindex, nofollow']);

        $this->assertDatabaseHas('seo_meta', [
            'seo_metaable_id' => $page->id,
            'follow_type' => 'noindex, nofollow',
        ]);
    }

    #[Test]
    public function follow_type_can_be_cleared(): void
    {
        $page = $this->createPage();

        $this->fillSeoMeta($page, ['follow_type' => null]);

        $this->assertDatabaseHas('seo_meta', [
            'seo_metaable_id' => $page->id,
            'follow_type' => null,
        ]);
    }

    #[Test]
    public function follow_type_that_is_not_plain_text_is_rejected(): void
    {
        $page = $this->createPage();

        try {
            $this->fillSeoMeta($page, [
                'follow_type' => ['isTrusted' => false],
            ]);
            $this->fail('Expected a validation exception.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('seo_meta', $exception->errors());
        }

        $this->assertDatabaseMissing('seo_meta', [
            'seo_metaable_id' => $page->id,
        ]);
    }

    private function createPage(): Page
    {
        $page = Page::factory()->make();
        $page->save();

        return $page;
    }

    /**
     * @param array<string,mixed> $seoMeta
     */
    private function fillSeoMeta(Page $page, array $seoMeta): void
    {
        $request = NovaRequest::create('/', 'POST', [
            'seo_meta' => json_encode(['title' => 'Title'] + $seoMeta),
        ]);

        SeoMeta::make('SEO', 'seo_meta')->fill($request, $page);
    }
}
