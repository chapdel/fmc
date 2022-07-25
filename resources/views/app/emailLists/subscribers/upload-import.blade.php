<x-mailcoach::card>
    <form class="form-grid" method="POST" action="{{ route('mailcoach.emailLists.import-subscribers', $emailList) }}">
        @csrf

        <div class="form-field">
            @error('replace_tags')
            <p class="form-error">{{ $message }}</p>
            @enderror

            <label class="label label-required" for="tags_mode">
                {{ __('mailcoach - Tags') }}
            </label>
            <div class="radio-group">
                <x-mailcoach::radio-field
                    name="replace_tags"
                    wire:model="replaceTags"
                    option-value="append"
                    :label="__('mailcoach - Append')"
                />
                <x-mailcoach::radio-field
                    name="replace_tags"
                    wire:model="replaceTags"
                    option-value="replace"
                    :label="__('mailcoach - Replace')"
                />
            </div>
        </div>

        <div class="form-field">
            @error('subscribeUnsubscribed')
            <p class="form-error">{{ $message }}</p>
            @enderror

            <div class="radio-group">
                <x-mailcoach::checkbox-field
                    name="subscribeUnsubscribed"
                    wire:model="subscribeUnsubscribed"
                    :label="__('mailcoach - Re-subscribe unsubscribed emails')"
                />
            </div>
        </div>

        <div class="form-field">
            @error('unsubscribeMissing')
            <p class="form-error">{{ $message }}</p>
            @enderror

            <div class="radio-group">
                <x-mailcoach::checkbox-field
                    name="unsubscribeMissing"
                    wire:model="unsubscribeMissing"
                    :label="__('mailcoach - Unsubscribe missing emails')"
                />
            </div>
        </div>

        <div class="flex gap-6">
            <div>
                <input accept=".csv,.txt,.xlsx" type="file" wire:model="file" />
                @error('file')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center h-10">
                <i class="far fa-arrow-right text-blue-300"></i>
            </div>
            <div class="flex items-center gap-4">
                <x-mailcoach::button wire:click.prevent="upload" :label="__('mailcoach - Import subscribers')" :disabled="!$file" />
                <div wire:loading wire:target="file">
                    <style>
                        @keyframes loadingpulse {
                            0%   {transform: scale(.8); opacity: .75}
                            100% {transform: scale(1); opacity: .9}
                        }
                    </style>
                    <span
                        style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                        class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                        <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                            @include('mailcoach::app.layouts.partials.logoSvg')
                        </span>
                    </span>
                    <span class="ml-1 text-gray-700">Uploading...</span>
                </div>
            </div>
        </div>

        @error('file')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <x-mailcoach::info>
            {!! __('mailcoach - Upload a CSV or XLSX file with these columns: email, first_name, last_name, tags <a href=":link" target="_blank">(see documentation)</a>', ['link' => 'https://mailcoach.app/docs/v5/mailcoach/using-mailcoach/audience#content-importing-subscribers']) !!}
        </x-mailcoach::info>
    </form>
</x-mailcoach::card>
