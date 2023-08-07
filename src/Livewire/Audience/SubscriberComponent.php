<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;

class SubscriberComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public Subscriber $subscriber;

    public string $email;

    public ?string $first_name = '';

    public ?string $last_name = '';

    public array $tags = [];

    public array $extra_attributes = [];

    public EmailList $emailList;

    public int $totalSendsCount;

    #[Url]
    public string $tab = 'profile';

    protected $listeners = [
        'tags-updated' => 'updateTags',
    ];

    protected function rules(): array
    {
        return [
            'email' => [
                'email:rfc',
                Rule::unique(self::getSubscriberTableName(), 'email')
                    ->where('email_list_id', $this->emailList->id)
                    ->ignore($this->subscriber->id),
            ],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'tags' => 'array',
            'extra_attributes' => ['nullable', 'array'],
        ];
    }

    public function updateTags(array|string ...$tags)
    {
        $this->tags = Arr::wrap($tags);
    }

    public function save()
    {
        $this->validate();

        /** @var UpdateSubscriberAction $updateSubscriberAction */
        $updateSubscriberAction = Mailcoach::getAudienceActionClass('update_subscriber', UpdateSubscriberAction::class);
        $updateSubscriberAction->execute(
            subscriber: $this->subscriber,
            attributes: [
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'extra_attributes' => $this->extra_attributes,
            ],
            tags: $this->tags ?? [],
        );

        notify(__mc('Subscriber :subscriber was updated.', ['subscriber' => $this->subscriber->email]));
    }

    public function mount(EmailList $emailList, Subscriber $subscriber)
    {
        $this->authorize('update', $subscriber);

        $this->fill($subscriber->toArray());

        $this->emailList = $emailList;
        $this->subscriber = $subscriber;
        $this->totalSendsCount = $subscriber->sends()->count();
        $this->tags = $subscriber->tags()->where('type', TagType::Default)->pluck('name')->toArray();
        $this->extraAttributes = $subscriber->extra_attributes->map(function ($value, $key) {
            return [
                'key' => $key,
                'value' => $value,
            ];
        })->where('key', '!=', '')->values()->toArray();

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Subscribers'), route('mailcoach.emailLists.subscribers', $this->emailList));
            });
    }

    public function addAttribute()
    {
        $this->extraAttributes[] = [];
    }

    public function saveAttributes()
    {
        $this->subscriber->extra_attributes = null;
        foreach ($this->extraAttributes as $extraAttribute) {
            $this->subscriber->extra_attributes[$extraAttribute['key']] = $extraAttribute['value'];
        }
        $this->subscriber->save();

        notify(__mc('Subscriber :subscriber was updated.', ['subscriber' => $this->subscriber->email]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.subscribers.show')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'emailList' => $this->emailList,
                'title' => $this->subscriber->email,
            ]);
    }
}
