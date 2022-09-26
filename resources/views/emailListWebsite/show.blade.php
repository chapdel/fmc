<x-mailcoach::layout-website :email-list="$emailList">
    @include('mailcoach::emailListWebsite.partials.header')

    <div class="w-full max-w-7xl mx-auto py-16 px-8">
        <div class="mt-16 border">
            <x-mailcoach::web-view :html="$webview"/>
        </div>

        <div class="mt-16 border-t text-sm text-gray-600 text-center pt-16">
            This content was sent via <a class="underline" href="https://mailcoach.app">Mailcoach</a>
        </div>
    </div>
</x-mailcoach::layout-website>
