<div>
    <div>
        <div class="mb-6">
            <x-mailcoach::template-chooser />
        </div>

        @if($template?->containsPlaceHolders())
            <div>
                @foreach($template->placeHolderNames() as $placeHolderName)
                    <div class="form-field max-w-full mb-6" wire:key="{{ $placeHolderName }}">
                        <label class="label" for="field_{{ $placeHolderName }}">
                            {{ \Illuminate\Support\Str::of($placeHolderName)->snake(' ')->ucfirst() }}
                        </label>

                        <textarea
                            class="input input-html"
                            rows="15"
                            wire:model.lazy="templateFieldValues.{{ $placeHolderName }}"
                        ></textarea>

                        @error('templateFieldValues.' . $placeHolderName)
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
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
                ></textarea>

                @error('templateFieldValues.html')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <x-mailcoach::campaign-replacer-help-texts/>
    </div>

    <x-mailcoach::editor-buttons :preview-html="$fullHtml" :model="$model" />
</div>
