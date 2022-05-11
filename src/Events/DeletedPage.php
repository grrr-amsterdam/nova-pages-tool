<?php

namespace Grrr\Pages\Events;

use Grrr\Pages\Models\Page;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeletedPage
{
    use Dispatchable, SerializesModels;

    public $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }
}
