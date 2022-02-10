<x-mailcoach::layout-campaign :title="__('mailcoach - Settings')" :campaign="$campaign">
    <form
        class="form-grid"
        action="{{ route('mailcoach.campaigns.settings', $campaign) }}"
        method="POST"
        data-dirty-check
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :value="$campaign->name" required :disabled="true" />

        <x-mailcoach::text-field :label="__('mailcoach - Subject')" name="subject" :value="$campaign->subject" :disabled="true" />

        <div>
            Sent to list "{{ $campaign->emailList->name }}"

            @if($campaign->tagSegment)
                Used segment {{ $campaign->tagSegment->name }}
            @endif
        </div>

        <x-mailcoach::fieldset :legend="__('mailcoach - Tracking')">
            <div class="form-field">
                <label class="label">{{ __('mailcoach - Track when…') }}</label>
                <div class="checkbox-group">
                    <x-mailcoach::checkbox-field :disabled="true" :label="__('mailcoach - Someone opens this email')" name="track_opens" :checked="$campaign->track_opens" />
                    <x-mailcoach::checkbox-field :disabled="true" :label="__('mailcoach - Links in the email are clicked')" name="track_clicks" :checked="$campaign->track_clicks" />
                </div>
            </div>

            <div class="form-field">
                <label class="label">{{ __('mailcoach - UTM Tags') }}</label>
                <div class="checkbox-group">
                    <x-mailcoach::checkbox-field :label="__('mailcoach - Automatically add UTM tags')" name="utm_tags" :checked="$campaign->utm_tags" :disabled="true" />
                </div>
            </div>
        </x-mailcoach::fieldset>
    </form>
</x-mailcoach::layout-campaign>
