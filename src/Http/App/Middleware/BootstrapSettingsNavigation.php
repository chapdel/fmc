<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Settings\SettingsNavigation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Navigation\Section;

class BootstrapSettingsNavigation
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        $navigation = resolve(SettingsNavigation::class);

        $navigation->add(__mc('Configuration'), route('general-settings'), function (Section $section) {
            $section
                ->add(__mc('General'), route('general-settings'))
                ->add(__mc('Mailers'), route('mailers'))
                ->add(__mc('Editor'), route('editor'))
                ->add(__mc('Webhooks'), route('webhooks'));
        });

        foreach (Mailcoach::$settingsMenuItems as $item) {
            $navigation->add($item->label, $item->url, function (Section $section) use ($item) {
                if (! $item->children) {
                    return null;
                }

                foreach ($item->children as $child) {
                    $section->add($child->label, $child->url);
                }
            });
        }

        return $next($request);
    }
}
