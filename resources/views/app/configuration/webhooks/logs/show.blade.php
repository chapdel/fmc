<x-mailcoach::card>
    <dl class="mt-8 dl max-w-full overflow-hidden">
        <dt>
            {{__mc('Sent at')}}
        </dt>
        <dd>
            {{ $webhookLog->created_at }}
        </dd>

        <dt>
            {{__mc('Status Code')}}
        </dt>
        <dd>
            <span class="inline-flex items-center">
                <x-mailcoach::rounded-icon
                    :type="$webhookLog->wasSuccessful() ? 'success' : 'error'"
                    :icon="$webhookLog->wasSuccessful() ? 'fa-fw fas fa-check' : 'fas fa-times'"
                />
                <span class="pl-2">{{ $webhookLog->status_code }}</span>
            </span>
        </dd>

        <dt>
            {{__mc('Event Type')}}
        </dt>
        <dd>
            {{ $webhookLog->event_type }}
        </dd>

        <dt>
            {{__mc('Attempt')}}
        </dt>
        <dd>
            {{ $webhookLog->attempt ?? __mc('Manual') }}
        </dd>

        <dt>
            {{__mc('URL')}}
        </dt>
        <dd>
            {{ $webhookLog->webhook_url }}
        </dd>

        <dt>
            {{__mc('Payload')}}
        </dt>
        <dd>
            <pre class="bg-gray-200 p-4 rounded">{{ json_encode($webhookLog->payload, JSON_PRETTY_PRINT) }}</pre>
        </dd>

        <dt>
            {{__mc('Response')}}
        </dt>
        <dd>
            <pre class="bg-gray-200 p-4 rounded overflow-x-scroll">{{ $this->getPrintableResponse() }}</pre>
        </dd>
    </dl>
</x-mailcoach::card>
