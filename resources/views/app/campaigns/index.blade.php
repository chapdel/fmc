<div>
    <div class="table-actions">
        @can('create', \Spatie\Mailcoach\Domain\Shared\Support\Config::getCampaignClass())
            @if ($totalListsCount || $totalCampaignsCount)
                <x-mailcoach::button x-on:click="$store.modals.open('create-campaign')" :label="__('mailcoach - Create campaign')" />

                <x-mailcoach::modal name="create-campaign" :title="__('mailcoach - Create campaign')" :confirm-text="__('mailcoach - Create campaign')">
                    <livewire:mailcoach::create-campaign />
                </x-mailcoach::modal>
            @endif
        @endcan

        @if($totalCampaignsCount)
            <div class="table-filters">
                <x-mailcoach::filters>
                    <x-mailcoach::filter :filter="$filter" value="" attribute="status">
                        {{ __('mailcoach - All') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($totalCampaignsCount) }}</span>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :filter="$filter" value="sent" attribute="status">
                        {{ __('mailcoach - Sent') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($sentCampaignsCount) }}</span>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :filter="$filter" value="scheduled" attribute="status">
                        {{ __('mailcoach - Scheduled') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($scheduledCampaignsCount) }}</span>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :filter="$filter" value="draft" attribute="status">
                        {{ __('mailcoach - Draft') }} <span class="counter">{{ Illuminate\Support\Str::shortNumber($draftCampaignsCount) }}</span>
                    </x-mailcoach::filter>
                    <x-mailcoach::filter :filter="$filter" value="automated" attribute="status">
                    </x-mailcoach::filter>
                </x-mailcoach::filters>
                <x-mailcoach::search wire:model="filter.search" :placeholder="__('mailcoach - Filter campaigns…')"/>
            </div>
        @endif
    </div>

    @if($totalCampaignsCount)
        <table class="table table-fixed">
            <thead>
                <tr>
                    <x-mailcoach::th class="w-4"></x-mailcoach::th>
                    <x-mailcoach::th :sort="$sort" property="name">{{ __('mailcoach - Name') }}</x-mailcoach::th>
                    <x-mailcoach::th :sort="$sort" property="email_list_id" class="w-48">{{ __('mailcoach - List') }}</x-mailcoach::th>
                    <x-mailcoach::th :sort="$sort" property="-sent_to_number_of_subscribers" class="w-24 th-numeric">{{ __('mailcoach - Emails') }}</x-mailcoach::th>
                    <x-mailcoach::th :sort="$sort" property="-unique_open_count" class="w-24 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Opens') }}</x-mailcoach::th>
                    <x-mailcoach::th :sort="$sort" property="-unique_click_count" class="w-24 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Clicks') }}</x-mailcoach::th>
                    <x-mailcoach::th :sort="$sort" property="-sent" sort-default class="w-48 th-numeric hidden | xl:table-cell">{{ __('mailcoach - Sent') }}</x-mailcoach::th>
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
</div>
