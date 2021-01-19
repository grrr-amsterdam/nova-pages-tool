<?php

namespace Grrr\Pages\Events;

use Grrr\Pages\Models\Page;

class SavingPage
{
    public $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }
}
