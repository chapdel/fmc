<?php

namespace Spatie\Mailcoach\Livewire\Webhooks\Forms;

use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EditWebhookForm extends Form
{
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    #[Rule('required')]
    public string $name;

    #[Rule('required', 'url', 'starts_with:https')]
    public string $url;

    #[Rule('required')]
    public string $secret;

    #[Rule('boolean')]
    public bool $use_for_all_lists;

    public ?array $emailLists = null;

    #[Rule('boolean')]
    public bool $enabled = true;

    #[Rule('boolean')]
    public bool $use_for_all_events = true;

    #[Rule('nullable', 'array')]
    public ?array $events = [];

    public function rules(): array
    {
        return [
            'emailLists' => ['nullable', 'array', 'required_if:webhook.use_for_all_lists,false'],
            'emailLists.*' => [\Illuminate\Validation\Rule::exists(self::getEmailListTableName(), 'id')],
        ];
    }

    public function setWebhook(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;

        $this->name = $webhook->name;
        $this->url = $webhook->url;
        $this->secret = $webhook->secret;
        $this->use_for_all_lists = $webhook->use_for_all_lists;
        $this->emailLists = $webhook->emailLists->pluck('id')->values()->toArray();
        $this->enabled = $webhook->enabled;
        $this->use_for_all_events = $webhook->useForAllEvents();
        $this->events = $webhook->events->toArray();
    }

    public function store(): void
    {
        $validated = $this->validate();

        if (isset($validated['enabled']) && $validated['enabled'] === true) {
            $validated['failed_attempts'] = 0;
        }

        $this->webhook->update(Arr::except($validated, ['emailLists']));
        $this->webhook->emailLists()->sync($this->emailLists);
    }
}
