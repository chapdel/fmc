<x-mailcoach::layout-main :title="__('mailcoach - Campaigns')">
        <div class="table-actions">
            @can('create', \Spatie\Mailcoach\Domain\Campaign\Models\Campaign::class)
            @if ($totalListsCount or $totalCampaignsCount)
                <x-mailcoach::button data-modal-trigger="create-campaign" :label="__('mailcoach - Create campaign')" />

                <x-mailcoach::modal :title="__('mailcoach - Create campaign')" name="create-campaign" :open="$errors->any()">
                    @include('mailcoach::app.campaigns.partials.create')
                </x-mailcoach::modal>
            @endif
            @endcan

            @if($totalCampaignsCount)
                <div class="table-filters">
                    <x-mailcoach::filters>
                        <x-mailcoach::filter active-on="" :queryString="$queryString" attribute="status">
                            {{ __('mailcoach - All') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalCampaignsCount) }}</span>
                        </x-mailcoach::filter>
                        <x-mailcoach::filter active-on="sent" :queryString="$queryString" attribute="status">
                            {{ __('mailcoach - Sent') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($sentCampaignsCount) }}</span>
                        </x-mailcoach::filter>
                        <x-mailcoach::filter active-on="scheduled" :queryString="$queryString" attribute="status">
                            {{ __('mailcoach - Scheduled') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($scheduledCampaignsCount) }}</span>
                        </x-mailcoach::filter>
                        <x-mailcoach::filter active-on="draft" :queryString="$queryString" attribute="status">
                            {{ __('mailcoach - Draft') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($draftCampaignsCount) }}</span>
                        </x-mailcoach::filter>
                        <x-mailcoach::filter active-on="automated" :queryString="$queryString" attribute="status">
                        </x-mailcoach::filter>
                    </x-mailcoach::filters>
                    <x-mailcoach::search :placeholder="__('mailcoach - Filter campaigns…')"/>
                </div>
            @endif
        </div>

        @if($totalCampaignsCount)
            <table class="table table-fixed">
                <thead>
                    <tr>
                        <x-mailcoach::th class="w-4"></x-mailcoach::th>
                        <x-mailcoach::th sort-by="name">{{ __('mailcoach - Name') }}</x-mailcoach::th>
                        <x-mailcoach::th sort-by="email_list_id" class="w-48">{{ __('mailcoach - List') }}</x-mailcoach::th>
                        <x-mailcoach::th sort-by="-sent_to_number_of_subscribers" class="w-24 th-numeric">{{ __('mailcoach - Emails') }}</x-mailcoach::th>
                        <x-mailcoach::th sort-by="-unique_open_count" class="w-24 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Opens') }}</x-mailcoach::th>
                        <x-mailcoach::th sort-by="-unique_click_count" class="w-24 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Clicks') }}</x-mailcoach::th>
                        <x-mailcoach::th sort-by="-sent" sort-default class="w-48 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Sent') }}</x-mailcoach::th>
                        <x-mailcoach::th class="w-12"></x-mailcoach::th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                    @include('mailcoach::app.campaigns.partials.row')
                    @endforeach
                </tbody>
            </table>

            <x-mailcoach::table-status :name="__('mailcoach - campaign|campaigns')" :paginator="$campaigns" :total-count="$totalCampaignsCount"
            :show-all-url="route('mailcoach.campaigns')"></x-mailcoach::table-status>
        @else
            @if ($totalListsCount)
                <x-mailcoach::help>
                    {{ __('mailcoach - No campaigns yet. Go write something!') }}
                </x-mailcoach::help>
            @else
                <x-mailcoach::help>
                    {!! __('mailcoach - No campaigns yet, but you‘ll need a list first, go <a href=":emailListsLink">create one</a>!', ['emailListsLink' => route('mailcoach.emailLists')]) !!}
                </x-mailcoach::help>
            @endif
        @endif

</x-mailcoach::layout-main>
