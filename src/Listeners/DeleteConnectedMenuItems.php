<?php

namespace Grrr\Pages\Listeners;

use Grrr\Pages\Events\DeletedPage;
use Outl1ne\MenuBuilder\Models\MenuItem;

class DeleteConnectedMenuItems
{
    public function handle(DeletedPage $event): void
    {
        MenuItem::query()
            ->where('data->page', $event->page->id)
            ->delete();
    }
}
