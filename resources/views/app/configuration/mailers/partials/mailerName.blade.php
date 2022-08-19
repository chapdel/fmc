<x-mailcoach::fieldset card :legend="__('Usage in Mailcoach API')">
    <div>
        Whenever you need to specify a <code>mailer</code> in the Mailcoach API and want to use this mailer, you'll need to pass this value:
        <p class="mt-2 ">
            <b><code>{{ $mailer->configName() }}</code></b>
        </p>
    </div>
</x-mailcoach::fieldset>
