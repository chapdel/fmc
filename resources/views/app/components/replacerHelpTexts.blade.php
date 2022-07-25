@if ($replacerHelpTexts())
    <button class="ml-auto link-dimmed" x-on:click.prevent="$store.modals.open('placeholders')">
        {{__('mailcoach - Placeholder cheat sheet')}}
    </button>

    <x-mailcoach::modal medium :dismissable="true" :title="__('mailcoach - Placeholder cheat sheet')" name="placeholders">
        <x-mailcoach::info class="markup-code">
            {{ __('mailcoach - You can use following placeholders in the subject and copy:') }}
        </x-mailcoach::info>
            <dl class="mt-4 markup-dl markup-code">
                @foreach($replacerHelpTexts as $replacerName => $replacerDescription)
                    <dt><code>::{{ $replacerName }}::</code></dt>
                    <dd>{{ $replacerDescription }}</dd>
                @endforeach
            </dl>
    </x-mailcoach::modal>
@endif
