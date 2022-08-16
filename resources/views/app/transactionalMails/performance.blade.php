<div class="card-grid">
<x-mailcoach::fieldset card :legend="__('mailcoach - Opens')">
        @if($transactionalMail->opens->count())
    <table class="mt-0 table-styled">
        <thead>
            <tr>
                <th>{{ __('mailcoach - Opened at') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactionalMail->opens as $open)
                <tr>
                    <td>{{ $open->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <x-mailcoach::info>{{ __('mailcoach - This mail hasn\'t been opened yet.') }}</x-mailcoach::info>
    @endif
</x-mailcoach::fieldset>

<x-mailcoach::fieldset card :legend="__('mailcoach - Clicks')">
    @if($transactionalMail->clicksPerUrl()->count())
        <table class="mt-0 table-styled">
            <thead>
                <tr>
                    <th>{{ __('mailcoach - URL') }}</th>
                    <th class="th-numeric">{{ __('mailcoach - Click count') }}</th>
                    <th class="th-numeric">{{ __('mailcoach - First clicked at') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($transactionalMail->clicksPerUrl() as $clickGroup)
                <tr class="markup-links">
                    <td><a href="{{ $clickGroup['url'] }}" target="_blank">{{ $clickGroup['url'] }}</a></td>
                    <td class="td-numeric">{{ $clickGroup['count'] }}</td>
                    <td class="td-numeric">{{ $clickGroup['first_clicked_at'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <x-mailcoach::info>
            {{ __('mailcoach - No links in this mail have been clicked yet.') }}
        </x-mailcoach::info>
    @endif
</x-mailcoach::fieldset>
</div>
