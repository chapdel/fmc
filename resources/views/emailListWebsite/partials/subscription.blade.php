<header class="w-full mb-4 p-4 sm:p-6 md:px-8 md:py-7 bg-yellow-50 border-b-2 border-yellow-500 text-xs text-gray-700">
    <div class="max-w-lg mx-auto space-y-2">
        <p>
            Every month I send out a newsletter like this one, containing lots of interesting stuff for the modern
            PHP
            developer.
        </p>
        <p>
            Subscribe to get the next edition in your mailbox.
        </p>
        <form
            action="{{ $emailList->incomingFormSubscriptionsUrl() }}"
            method="post"
            accept-charset="utf-8"
            class="flex flex-col md:flex-row items-stretch {{ $class ?? '' }}"
        >
            {{-- this is a honeypost field --}}
            <input type="text" name="username" style="display:none !important" tabindex="-1" autocomplete="off">

            <input class="mb-2 md:mb-0" type="email" autocomplete="off" id="email" name="email"
                   placeholder="Your e-mail address" aria-label="E-mail" required>

            <input type="submit" name="submit" id="submit" value="Subscribe"
                   class="px-3 py-2 text-sm text-white bg-yellow-500 font-semibold border-t-3 border-b-3 border-yellow-700 border-t-transparent">
        </form>

        @error('email')
        <div
            class="mt-2 py-2 px-2 flex-1 bg-red-500 focus:outline-none md:mb-0 text-white text-2xs">{{ $message }}</div>
        @enderror

    </div>
</header>
