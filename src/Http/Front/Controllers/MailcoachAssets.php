<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Livewire\Controllers\CanPretendToBeAFile;

class MailcoachAssets
{
    use CanPretendToBeAFile;

    public function script()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../../../../resources/dist/app.js');
    }

    public function style()
    {
        return $this->pretendResponseIsFile(__DIR__.'/../../../../resources/dist/app.css', 'text/css');
    }
}
