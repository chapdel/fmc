<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Send webhook') }}
        <span class="form-legend-accent">
            @if ($url)
                to {{ $url }}
            @endif
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12">
            <x-mailcoach::help full>
                <p>
                    {!! __mc('These webhooks will use the same signature validation as documented for the <a href=":url" target="_blank">event webhooks</a>', [
                        'url' => 'https://mailcoach.app/docs/self-hosted/v6/using-mailcoach/webhooks/configuring-webhooks',
                    ]) !!}
                </p>
                <details>
                    <summary>{{ __mc('Example payload') }}</summary>
                    <pre class="max-w-full code overflow-x-auto relative z-10 bg-white mt-1"><!--
-->{
    "automation_name": "{{ $automation->name }}",
    "automation_uuid": "{{ $automation->uuid }}",
    "subscriber": {
        "email_list_uuid": "{{ $automation->emailList->uuid }}",
        "email": "john@doe.com",
        "first_name": null,
        "last_name": null,
        "extra_attributes": [],
        "tags": [],
        "uuid": "{{ \Illuminate\Support\Str::uuid() }}",
        "subscribed_at": "{{ now()->startOfSecond()->toJSON() }}",
        "unsubscribed_at": null,
        "created_at": "{{ now()->startOfSecond()->toJSON() }}",
        "updated_at": "{{ now()->startOfSecond()->toJSON() }}"
    }
}</pre>
                </details>
            </x-mailcoach::help>
        </div>
        <div class="col-span-12 md:col-span-6">
            <x-mailcoach::text-field
                :label="__mc('Url')"
                name="url"
                wire:model="url"
            />
        </div>

        <div class="col-span-12 md:col-span-6">
            <x-mailcoach::text-field
                :label="__mc('Secret')"
                name="secret"
                wire:model="secret"
            />
        </div>
    </x-slot>

</x-mailcoach::automation-action>
