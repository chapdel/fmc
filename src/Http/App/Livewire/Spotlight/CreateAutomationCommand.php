<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __('mailcoach - Create automation');
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('name')->setPlaceholder('Automation name')->setType(SpotlightCommandDependency::INPUT));
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('create', self::getAutomationClass());
    }

    public function execute(Spotlight $spotlight, string $name)
    {
        if (! $name) {
            return;
        }

        $automation = resolve(CreateAutomationAction::class)->execute([
            'name' => $name,
        ]);

        flash()->success(__('mailcoach - Automation :automation was created.', ['automation' => $automation->name]));

        $spotlight->redirect(route('mailcoach.automations.settings', $automation));
    }
}
