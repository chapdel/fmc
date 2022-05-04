<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomation extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public ?string $name = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
        ];
    }

    public function saveAutomation()
    {
        $automation = resolve(CreateAutomationAction::class)->execute(
            $this->validate(),
        );

        flash()->success(__('mailcoach - Automation :automation was created.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $automation);
    }

    public function render()
    {
        return view('mailcoach::app.automations.partials.create');
    }
}
