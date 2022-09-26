<?php /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */ ?>
<x-mailcoach::layout-website :email-list="$emailList">
    @include('mailcoach::emailListWebsite.partials.header')

    <div class="w-full max-w-7xl mx-auto py-16 px-8 mt-8">
        @if($campaigns->count() > 0)
            <div>
                <ul class="space-y-8 divide-y divide-gray-200">
                    @foreach($campaigns as $campaign)
                        <li class="pt-10">
                            <a href="{{ $campaign->websiteUrl() }}">
                                <div class="flex justify-between">
                                    <div>
                                        <span class="text-xs text-gray-600 uppercase">{{ $campaign->sent_at->toFormattedDateString() }}</span>
                                        <h2 class="hover:underline font-medium text-2xl">{{ $campaign->subject }}</h2>
                                        <div>
                                            {!! $campaign->getSummary() !!}
                                        </div>
                                    </div>

                                    @if ($src = $campaign->getSummaryImage())
                                        <div class="w-1/5">
                                            <img class="w-full h-auto" src="{{ $src }}" alt="">
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="flex mt-8 justify-between">
                    @if($campaigns->previousPageUrl())
                        <div class="cursor-pointer border px-4 py-2 rounded hover:bg-gray-100">
                            @if($campaigns->previousPageUrl())
                                <span class="text-gray-300 mr-1"><<</span> Newer
                            @endif
                        </div>
                    @else
                        <div></div>
                    @endif


                    <div class="cursor-pointer border px-4 py-2 rounded hover:bg-gray-100">
                        <a href="{{ $campaigns->nextPageUrl() }}">
                            Older <span class="text-gray-300 ml-1">>></span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="border-t mt-16"></div>

            <div class="mt-16 text-3xl font-semibold text-center">
                No campaigns have been sent yet...
            </div>
        @endif
    </div>

    <div class="mt-16 text-sm text-gray-600 text-center pt-16">
        Powered by <a class="underline" href="https://mailcoach.app">Mailcoach</a>
    </div>
</x-mailcoach::layout-website>
