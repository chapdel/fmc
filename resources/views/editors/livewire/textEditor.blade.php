<div class="form-grid">
    @if ($model->hasTemplates())
        <x-mailcoach::template-chooser />
    @endif

    @if($template?->containsPlaceHolders())
        @foreach($template->placeHolderNames() as $placeHolderName)
            <div class="form-field max-w-full" wire:key="{{ $placeHolderName }}">
                <label class="label" for="field_{{ $placeHolderName }}">
                    {{ \Illuminate\Support\Str::of($placeHolderName)->snake(' ')->ucfirst() }}
                </label>

                <textarea
                    class="input input-html"
                    rows="15"
                    wire:model.lazy="templateFieldValues.{{ $placeHolderName }}"
                ></textarea>

                @error('templateFieldValues.' . $placeHolderName)
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    @else
        <div class="form-field max-w-none">
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
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <x-mailcoach::replacer-help-texts :model="$model" />

    <x-mailcoach::editor-buttons :preview-html="$fullHtml" :model="$model" />
</div>
