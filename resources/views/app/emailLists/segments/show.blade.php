<div>
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'details')" :active="$tab === 'details'">
                {{ __('mailcoach - Segment details') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'population')" :active="$tab === 'population'">
                <x-mailcoach::icon-label :text="__('mailcoach - Population')" invers :count="$selectedSubscribersCount" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @if ($tab === 'details')
        <form
            class="form-grid"
            wire:submit.prevent="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            method="POST"
        >
            @if (! $emailList->tags()->count())
                <x-mailcoach::info>
                    <div class="markup-lists">
                        {{ __('mailcoach - A segment is based on tags.') }}
                        <ol class="mt-4">
                            <li>{!! __('mailcoach - <a href=":tagLink">Create some tags</a> for this list first.', ['tagLink' => route('mailcoach.emailLists.tags', $emailList)]) !!}</li>
                            <li>{!! __('mailcoach - Assign these tags to some of the <a href=":subscriberslink">subscribers</a>.', ['subscriberslink' => route('mailcoach.emailLists.subscribers', $emailList)]) !!}</li>
                        </ol>
                    </div>
                </x-mailcoach::info>
            @endif

            @csrf
            @method('PUT')

            <x-mailcoach::text-field :label="__('mailcoach - Name')" name="segment.name" wire:model.lazy="segment.name" type="name" required />

            <div class="form-field">
                <label class=label>{{ __('mailcoach - Include with tags') }}</label>
                <div class="flex items-end">
                    <div class="flex-none">
                        <x-mailcoach::select-field
                            name="positive_tags_operator"
                            wire:model="positive_tags_operator"
                            :options="['any' => __('mailcoach - Any'), 'all' => __('mailcoach - All')]"
                        />
                    </div>
                    <div class="ml-2 flex-grow">
                        <x-mailcoach::tags-field
                            name="positive_tags"
                            :value="$positive_tags"
                            :tags="$emailList->tags()->pluck('name')->toArray()"
                        />
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label class=label>{{ __('mailcoach - Exclude with tags') }}</label>
                <div class="flex items-end">
                    <div class="flex-none">
                        <x-mailcoach::select-field
                            name="negative_tags_operator"
                            wire:model="negative_tags_operator"
                            :options="['any' => __('mailcoach - Any'), 'all' => __('mailcoach - All')]"
                        />
                    </div>
                    <div class="ml-2 flex-grow">
                        <x-mailcoach::tags-field
                            name="negative_tags"
                            :value="$negative_tags"
                            :tags="$emailList->tags()->pluck('name')->toArray()"
                        />
                    </div>
                </div>
            </div>

            <div class="form-buttons">
                <x-mailcoach::button :label="__('mailcoach - Save segment')" />
            </div>
        </form>
    @endif

    @if($tab === 'population')
        <livewire:mailcoach::segment-subscribers :emailList="$emailList" :segment="$segment" />
    @endif
</div>
