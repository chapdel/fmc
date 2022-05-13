<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\UpdateCampaignAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTransactionalTemplateCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __('mailcoach - Create transactional template');
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('name')->setPlaceholder('Template name')->setType(SpotlightCommandDependency::INPUT));
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('create', self::getTemplateClass());
    }

    public function execute(Spotlight $spotlight, string $name)
    {
        if (! $name) {
            return;
        }

        $template = resolve(CreateTemplateAction::class)->execute([
            'name' => $name,
        ]);

        flash()->success(__('mailcoach - Template :template was created.', ['template' => $template->name]));

        $spotlight->redirect(route('mailcoach.templates.edit', $template));
    }
}
