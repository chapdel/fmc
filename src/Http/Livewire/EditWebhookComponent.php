<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Settings\Enums\WebhookEventTypes;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditWebhookComponent extends Component
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    public array $emailLists;

    public array $eventOptions = [
    ];

    public bool $useForAllEvents = false;

    public function rules(): array
    {
        $rules = [
            'webhook.name' => ['required'],
            'webhook.url' => ['required', 'url', 'starts_with:https'],
            'webhook.secret' => ['required'],
            'webhook.use_for_all_lists' => ['boolean'],
            'emailLists' => ['nullable', 'array', 'required_if:webhook.use_for_all_lists,false'],
            'emailLists.*' => [Rule::exists(self::getEmailListTableName(), 'id')],
        ];

        if (config('mailcoach.webhooks.selectable_event_types_enabled', false)) {
            $rules['webhook.use_for_all_events'] = ['boolean'];
            $rules['webhook.events'] = ['nullable', 'array'];
        }

        return $rules;
    }

    public function mount(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;

        $this->emailLists = $webhook->emailLists->pluck('id')->values()->toArray();

        foreach (WebhookEventTypes::cases() as $eventType) {
            $this->eventOptions[$eventType->value()] = $eventType->label();
        }
    }

    public function save()
    {
        $this->webhook->update($this->validate()['webhook']);
        $this->webhook->emailLists()->sync($this->emailLists);

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
}
