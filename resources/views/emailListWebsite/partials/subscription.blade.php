<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */ ?>
<header>
    <div class="max-w-lg space-y-2">
        @if ($emailList->description)
            {{ $emailList->website_intro }}
        @endif
        <div class="font-bold tracking-tight ">
            Subscribe to the newsletter
        </div>
        <form
            action="{{ $emailList->incomingFormSubscriptionsUrl() }}"
            method="post"
            accept-charset="utf-8"
            class=""
        >
            {{-- this is a honeypot field --}}
            <input type="text" name="username" style="display:none !important" tabindex="-1" autocomplete="off">

            <div class="flex space-x-4">
            <input
                type="email"
                autocomplete="off"
                id="email"
                name="email"
                placeholder="Your e-mail "
                aria-label="E-mail"
                required
                class="border-2 rounded w-full max-w-[300px] py-2 px-4 text-gray-700 leading-tight"
            >

            <input
                type="submit"
                name="submit"
                id="submit"
                value="Subscribe"
                class="cursor-pointer hover:opacity-80 text-white py-2 px-4 rounded-md"
                style="
                    background: {{ $emailList->getWebsitePrimaryColor() }};
                    color: {{ $emailList->getWebsiteContrastingTextColor() }}
                "
            >
            </div>
        </form>

        @error('email')
        <div class="text-red-400 text-sm mt-1">
            {{ $message }}
        </div>
        @enderror
    </div>
</header>
