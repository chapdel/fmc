<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Template as TemplateModel;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\ValidationRules\Rules\Delimited;

class Template extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public TemplateModel $template;

    protected function rules(): array
    {
        return [
            'template.name' => 'required',
            'template.html' => 'required',
            'template.structured_html' => 'nullable',
        ];
    }

    public function mount(TemplateModel $template)
    {
        $this->authorize('update', $template);

        $this->template = $template;
    }

    public function save()
    {
        $this->validate();

        $this->template->save();

        $this->flash(__('mailcoach - Template :template was updated.', ['template' => $this->template->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.campaigns.templates.edit')
            ->layout('mailcoach::app.layouts.main', [
                'title' => $this->template->name,
            ]);
    }
}
