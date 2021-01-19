<?php

namespace Grrr\Pages\Listeners;

use Grrr\Pages\Events\SavedPage;

class UpdateChildPageUrls
{
    public function handle(SavedPage $event): void
    {
        if ($event->page->isDirty(['slug', 'parent_id'])) {
            // Trigger a SavingPage event for each child page.
            // This should update their URLs automatically.
            $event->page->children->each->save();
        }
    }
}
