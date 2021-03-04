<?php

namespace Tests\Unit\Models;

use Grrr\Pages\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Tests\Unit\TestCase;

class PageTest extends TestCase
{
    /** @test */
    public function table_has_expected_columns(): void
    {
        $expectedColumns = [
            'id',
            'title',
            'slug',
            'content',
            'url',
            'content',
            'status',
        ];
        $this->assertTrue(Schema::hasTable('grrr_nova_pages'));
        $this->assertTrue(
            Schema::hasColumns('grrr_nova_pages', $expectedColumns),
            'Pages table does not have expected columns'
        );
    }

    /** @test */
    public function page_gets_composed_url(): void
    {
        $parent = Page::factory()->make(['slug' => '']);
        $parent->save();

        $this->assertSame($parent->url, '/');

        $child = Page::factory()->make(['slug' => 'services']);
        $child->parent()->associate($parent);
        $child->save();

        $this->assertSame($child->url, '/services');

        $grandchild = Page::factory()->make(['slug' => 'web-design']);
        $grandchild->parent()->associate($child);
        $grandchild->save();

        $this->assertSame($grandchild->url, '/services/web-design');
    }

    /** @test */
    public function page_url_will_adapt_to_changing_parent(): void
    {
        $parent1 = Page::factory()->make(['slug' => 'services']);
        $parent1->save();

        $child = Page::factory()->make(['slug' => 'web-design']);
        $child->parent()->associate($parent1);
        $child->save();

        $this->assertSame($child->url, '/services/web-design');

        $parent2 = Page::factory()->make(['slug' => 'projects']);
        $parent2->save();

        // Moving the child to a new parent will re-generate the URL.
        $child->parent()->associate($parent2);
        $child->save();

        $this->assertSame($child->url, '/projects/web-design');

        // Updating a parent's slug will also re-generate the URL.
        $parent2->refresh();
        $parent2->slug = 'stuff';
        $parent2->save();

        $child->refresh();
        $this->assertSame($child->url, '/stuff/web-design');
    }

    /** @test */
    public function page_accepts_metadata(): void
    {
        $page = Page::factory()->make(['slug' => 'web-design']);
        $page->metadata = [
            'foo' => 123,
            'bar' => false,
            'qux' => 'lorem ipsum',
        ];
        $page->save();

        $freshPage = $page->fresh();
        $this->assertSame(123, $freshPage->metadata['foo']);

        $this->assertSame(false, $freshPage->metadata['bar']);
        $this->assertSame("lorem ipsum", $freshPage->metadata['qux']);
    }

    public function it_may_have_translations(): void
    {
        $page = Page::factory()->create();
        $this->assertInstanceOf(Collection::class, $page->translations);
    }

    /** @test */
    public function you_can_add_a_translation(): void
    {
        $pageEn = Page::factory()->create(['language' => 'en']);
        $pageNl = Page::factory()->create(['language' => 'nl']);

        $pageEn->translations()->attach($pageNl->id);

        $pageEn->refresh();
        $this->assertCount(1, $pageEn->translations);
        $this->assertInstanceOf(Page::class, $pageEn->translations->first());
    }

    /** @test */
    public function setting_a_translation_goes_both_ways(): void
    {
        $pageEn = Page::factory()->create(['language' => 'en']);
        $pageNl = Page::factory()->create(['language' => 'nl']);

        $pageEn->translations()->attach($pageNl);

        $pageEn->refresh();
        $pageNl->refresh();

        $this->assertCount(1, $pageEn->translations);
        $this->assertInstanceOf(Page::class, $pageEn->translations->first());

        $this->assertCount(1, $pageNl->translations);
        $this->assertInstanceOf(Page::class, $pageNl->translations->first());
    }
}
