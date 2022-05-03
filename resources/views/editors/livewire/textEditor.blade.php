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
                            wire:model="fields.{{ $placeHolderName }}"
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
                        wire:model="fields.html"
                    />
                </div>
            @endif

            <textarea class="hidden"
                      data-html-preview-source
                      wire:model="fullHtml"
            ></textarea>

            {{--
            <x-mailcoach::modal
                :title="__('mailcoach - Preview') . ' - ' .
            $campaign->subject" name="preview" large
                :open="Request::get('modal')">
                <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
            </x-mailcoach::modal>
            --}}

            <x-mailcoach::campaign-replacer-help-texts/>

            {{-- End test dialog --}}
        </div>

    </div>

    <div class="form-buttons">
        <x-mailcoach::button wire:click="save" :label="__('mailcoach - Save content')"/>

        {{-- Start test dialog --}}
        <x-mailcoach::text-field
            :label="__('mailcoach - Test addresses')"
            :placeholder="__('mailcoach - Email(s) comma separated')"
            name="emails"
            :required="true"
            type="text"
            wire:model="emails"
            :value="cache()->get('mailcoach-test-email-addresses')"
        />

        <x-mailcoach::button type="" wire:click="sendTest" class="ml-2"
                             :label="__('mailcoach - Save and send test')"/>
    </div>
</div>
