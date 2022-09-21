<form
    method="POST"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::card>
        <x-mailcoach::help>
            <p>
                Mailcoach can create a website that displays the content of each mail you send to this list. This way,
                people not subscribed on your list can still read your content.
            </p>
            <p>
                @if($emailList->has_website)
                    Your website is available at <a
                        href="{{ $emailList->websiteUrl() }}">{{ $emailList->websiteUrl() }}</a>
                @endif
            </p>
        </x-mailcoach::help>

        <x-mailcoach::checkbox-field
            :label="__('mailcoach - Enable website')"
            name="emailList.has_website"
            wire:model.lazy="emailList.has_website"
        />

        <x-mailcoach::text-field
            :label="__('mailcoach - Slug')"
            name="emailList.website_slug"
            wire:model.lazy="emailList.website_slug"
        />

        <x-mailcoach::info>
            {{ __('You can choose a url where you website will be displayed.') }}
        </x-mailcoach::info>

        <x-mailcoach::textarea-field
            :label="__('mailcoach - Intro')"
            name="emailList.website_intro"
            wire:model.lazy="emailList.website_intro"
        />

        <x-mailcoach::info>
            {{ __('This text will be display at the top of the page.') }}
        </x-mailcoach::info>

        <x-mailcoach::checkbox-field
            :label="__('mailcoach - Allow subscriptions')"
            name="emailList.show_subscription_form_on_website"
            wire:model.lazy="emailList.show_subscription_form_on_website"
        />

        <x-mailcoach::info>
            {{ __('mailcoach - When enabled, a subscription from will be displayed on the website.') }}
        </x-mailcoach::info>

        <div class="gap-6">
            <div>
                <label class="label" for="image">
                    Header Image
                </label>
            </div>
            <div class="mt-2 max-w-sm">
                <input accept=".jpg,.png" type="file" wire:model="image"/>
                @error('image')
                <p class="form-error mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-4">
                <div wire:loading.delay wire:target="image">
                    <style>
                        @keyframes loadingpulse {
                            0% {
                                transform: scale(.8);
                                opacity: .75
                            }
                            100% {
                                transform: scale(1);
                                opacity: .9
                            }
                        }
                    </style>
                    <span
                        style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                        class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                            <span
                                class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </span>
                    <span class="ml-1 text-gray-700">Uploading...</span>
                </div>
            </div>
        </div>

        <div>
            @if ($headerImageUrl = $emailList->websiteHeaderImageUrl())
                <img class="max-w-lg" alt="uploaded header image" src="{{ $headerImageUrl }}"/>
            @endif
        </div>

        <x-mailcoach::info>
            This image will be display at the top of your website. Only .jpg and .png are allowed, the maximum size is 2MB.
        </x-mailcoach::info>

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__('mailcoach - Save')"/>
        </x-mailcoach::form-buttons>
    </x-mailcoach::card>
</form>

