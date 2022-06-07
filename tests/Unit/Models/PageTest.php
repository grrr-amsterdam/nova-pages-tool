<?php

namespace Tests\Unit\Models;

use Grrr\Pages\Models\Page;
use Grrr\Pages\Models\PageTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Outl1ne\MenuBuilder\Models\MenuItem;
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
        $pageNl->refresh();

        $this->assertCount(1, $pageEn->translations);
        $this->assertInstanceOf(Page::class, $pageEn->translations->first());
        $this->assertTrue($pageNl->is($pageEn->translations->first()));

        // Translations are added bidirectionally.
        $this->assertCount(1, $pageNl->translations);
        $this->assertInstanceOf(Page::class, $pageNl->translations->first());
        $this->assertTrue($pageEn->is($pageNl->translations->first()));
    }

    /**
     * @test
     */
    public function deleting_a_page_will_delete_menu_items(): void
    {
        // Prepare pages
        $page1 = Page::factory()->create();
        $page2 = Page::factory()->create();

        // Create menu items
        $menuItem1 = MenuItem::create([
            'name' => 'One for page 1',
            'locale' => 'nl',
            'order' => 1,
            'data' => ['page' => $page1->id],
        ]);
        $menuItem2 = MenuItem::create([
            'name' => 'One for page 2',
            'locale' => 'nl',
            'order' => 2,
            'data' => ['page' => $page2->id],
        ]);
        $menuItem3 = MenuItem::create([
            'name' => 'Another one for page 2',
            'locale' => 'nl',
            'order' => 2,
            'data' => ['page' => $page2->id],
        ]);

        // Sanity check.
        $menuItems = MenuItem::all();
        $this->assertCount(3, $menuItems);

        // Start deleting pages.
        $page1->delete();

        $menuItems = MenuItem::all();
        $this->assertCount(2, $menuItems);

        $this->assertNotNull(
            $menuItems->first(fn(MenuItem $item) => $item->is($menuItem2))
        );
        $this->assertNotNull(
            $menuItems->first(fn(MenuItem $item) => $item->is($menuItem3))
        );

        $page2->delete();

        $menuItems = MenuItem::all();
        $this->assertCount(0, $menuItems);
    }

    /** @test */
    public function translations_should_stay_in_sync(): void
    {
        $pageEn = Page::factory()->create(['language' => 'en']);
        $pageNl = Page::factory()->create(['language' => 'nl']);
        $anotherPageNl = Page::factory()->create(['language' => 'nl']);

        $pageEn->translations()->attach($pageNl->id);

        $pageEn->refresh();
        $pageNl->refresh();

        $pageEn->translations()->detach($pageNl->id);
        $pageEn->translations()->attach($anotherPageNl->id);

        $this->assertCount(0, $pageNl->translations);
    }

    /** @test */
    public function translations_will_be_deleted_when_relations_are_removed(): void
    {
        $pageEn = Page::factory()->create(['language' => 'en']);
        $pageNl = Page::factory()->create(['language' => 'nl']);

        $pageEn->translations()->attach($pageNl->id);

        $pageEn->refresh();
        $this->assertDatabaseCount('grrr_nova_page_translations', 2);

        $pageNl->delete();
        $pageEn->refresh();
        $this->assertDatabaseCount('grrr_nova_page_translations', 0);
    }
}
