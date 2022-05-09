<div>
    <div>
        <div>
            <div class="mb-6">
                <x-mailcoach::template-chooser />
            </div>
            @if($template?->containsPlaceHolders())
                @foreach($template->placeHolderNames() as $placeHolderName)
                    <div class="form-field max-w-full mb-6">
                        <label class="label" for="field_{{ $placeHolderName }}">
                            {{ \Illuminate\Support\Str::of($placeHolderName)->snake(' ')->ucfirst() }}
                        </label>

                        <textarea
                            class="input input-html"
                            rows="15"
                            wire:model.lazy="templateFieldValues.{{ $placeHolderName }}"
                        ></textarea>
                    </div>
                @endforeach
            @else
                <div>
                    <label class="label" for="field_html">
                        HTML
                    </label>

                    <textarea
                        class="input input-html"
                        name="field_html"
                        rows="15"
                        wire:model.lazy="templateFieldValues.html"
                    />
                </div>
            @endif


            <x-mailcoach::campaign-replacer-help-texts/>

            {{-- End test dialog --}}
        </div>

    </div>

    <div class="form-buttons">
        <x-mailcoach::button-secondary x-on:click.prevent="$store.modals.open('preview')" :label="__('mailcoach - Preview')"/>
        <x-mailcoach::preview-modal name="preview" :html="$fullHtml" :title="__('mailcoach - Preview') . ' - ' . $campaign->subject" />

        <x-mailcoach::button wire:click="save" :label="__('mailcoach - Save content')"/>

        <x-mailcoach::button x-on:click.prevent="$wire.save() && $store.modals.open('send-test')" class="ml-2" :label="__('mailcoach - Save and send test')"/>
        <x-mailcoach::modal name="send-test">
            <livewire:mailcoach::send-test :model="$campaign" />
        </x-mailcoach::modal>
    </div>
</div>
