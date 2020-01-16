<?php

namespace Spatie\Mailcoach\Http\App\ViewComposers;

use Illuminate\View\View;
use Spatie\Mailcoach\Support\Version;

class FooterComposer
{
    public function compose(View $view)
    {
        $view->with('versionInfo', app(Version::class));
    }
}
