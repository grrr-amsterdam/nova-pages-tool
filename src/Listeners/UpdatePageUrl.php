<?php

namespace Grrr\Pages\Listeners;

use Grrr\Pages\Events\SavingPage;
use Grrr\Pages\Models\Page;

class UpdatePageUrl
{
    public function handle(SavingPage $event): void
    {
        $event->page->url = $this->composeUrl($event->page);
    }

    private function composeUrl(?Page $page): string
    {
        return !$page
            ? ''
            : (!$page->parent_id
                ? '/' . $page->slug
                : rtrim($this->composeUrl($page->parent), '/') .
                    '/' .
                    $page->slug);
    }
}
