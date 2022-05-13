<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use Illuminate\Http\Request;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use LivewireUI\Spotlight\SpotlightCommandDependencies;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightSearchResult;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class ShowTransactionalTemplateCommand extends SpotlightCommand
{
    use UsesMailcoachModels;

    public function getName(): string
    {
        return __('mailcoach - Show :resource', ['resource' => 'transactional template']);
    }

    public function getSynonyms(): array
    {
        return [
            __('mailcoach - View :resource', ['resource' => 'transactional template']),
            __('mailcoach - Go :resource', ['resource' => 'transactional template']),
        ];
    }

    public function dependencies(): ?SpotlightCommandDependencies
    {
        return SpotlightCommandDependencies::collection()
            ->add(SpotlightCommandDependency::make('template')->setPlaceholder('Template')->setType(SpotlightCommandDependency::SEARCH));
    }

    public function searchTemplate($query)
    {
        return self::getTransactionalMailTemplateClass()::where('name', 'like', "%$query%")
            ->get()
            ->map(function(TransactionalMailTemplate $template) {
                return new SpotlightSearchResult(
                    $template->id,
                    $template->name,
                    null,
                );
            });
    }

    public function shouldBeShown(Request $request): bool
    {
        return $request->user()->can('view', self::getTransactionalMailTemplateClass());
    }

    public function execute(Spotlight $spotlight, Template $template)
    {
        $spotlight->redirect(route('mailcoach.transactionalMails.templates.edit', $template));
    }
}
