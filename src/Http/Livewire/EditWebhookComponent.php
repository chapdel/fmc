<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfigurationEvent;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditWebhookComponent extends Component
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    public array $email_lists;
    public array $event_options = [
        'SubscribedEvent',
        'UnconfirmedSubscriberCreatedEvent',
        'UnsubscribedEvent',
        'CampaignSentEvent',
        'TagAddedEvent',
        'TagRemovedEvent'
    ];
    public array $selected_events = [];
    public bool $use_for_all_events = false;

    public function rules(): array
    {
        $rules = [
            'webhook.name' => ['required'],
            'webhook.url' => ['required', 'url', 'starts_with:https'],
            'webhook.secret' => ['required'],
            'webhook.use_for_all_lists' => ['boolean'],
            'email_lists' => ['nullable', 'array', 'required_if:webhook.use_for_all_lists,false'],
            'email_lists.*' => [Rule::exists(self::getEmailListTableName(), 'id')],
        ];

        if (config('mailcoach.webhooks.selectable_event_types_enabled', false)) {
            $rules['webhook.use_for_all_events'] = ['boolean'];
        }

        return $rules;
    }

    public function mount(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;

        $this->email_lists = $webhook->emailLists->pluck('id')->values()->toArray();

        if (config('mailcoach.webhooks.selectable_event_types_enabled', false)) {
            $this->selected_events = $webhook->events->pluck('name')->values()->toArray();
        }
    }

    public function save()
    {
        $this->webhook->update($this->validate()['webhook']);
        $this->webhook->emailLists()->sync($this->email_lists);
        $this->syncSelectedEvents();

        $this->flash(__mc('The webhook has been updated.'));
    }

    public function render()
    {
        $emailListNames = self::getEmailListClass()::query()
            ->pluck('name', 'id')
            ->toArray();

        return view('mailcoach::app.configuration.webhooks.edit', [
            'emailListNames' => $emailListNames,
        ])->layout('mailcoach::app.layouts.settings', [
            'title' => $this->webhook->name,
        ]);
    }

    protected function syncSelectedEvents(): void
    {
        if (! config('mailcoach.webhooks.selectable_event_types_enabled', false)) {
            return;
        }

        // Remove events on the webhook configuration that are not in the selected events
        $this->webhook->events()
            ->whereNotIn('name', $this->selected_events)
            ->delete();

        // Add the events that are in selected events but not on the webhook configuration
        foreach ($this->selected_events as $selectedEvent) {
            WebhookConfigurationEvent::updateOrCreate([
                'webhook_configuration_id' => $this->webhook->id,
                'name' => $selectedEvent
            ]);
        }
    }
}
