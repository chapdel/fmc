<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Audience\Actions\EmailLists\CreateEmailListAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\UpdateCampaignAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateListCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __('mailcoach - Create list');
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('name')->setPlaceholder('List name')->setType(SpotlightCommandDependency::INPUT))
            ->add(SpotlightCommandDependency::make('fromEmail')->setPlaceholder('Default from email')->setType(SpotlightCommandDependency::INPUT))
            ->add(SpotlightCommandDependency::make('fromName')->setPlaceholder('Default from name')->setType(SpotlightCommandDependency::INPUT));
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('create', self::getEmailListClass());
    }

    public function execute(Spotlight $spotlight, string $name, string $fromEmail, string $fromName)
    {
        if (! $name || !$fromEmail) {
            return;
        }

        $listClass = self::getEmailListClass();

        $list = resolve(CreateEmailListAction::class)->execute(new $listClass, [
            'name' => $name,
            'default_from_email' => $fromEmail,
            'default_from_name' => $fromName,
        ]);

        flash()->success(__('mailcoach - List :emailList was created', ['emailList' => $list->name]));

        $spotlight->redirect(route('mailcoach.emailLists.general-settings', $list));
    }
}
