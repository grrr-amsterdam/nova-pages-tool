<?php

namespace Tests\Unit\Models;

use Grrr\Pages\Models\Page;
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
}
