    <x-mailcoach::fieldset card :legend="__('mailcoach - Audience')">
        @error('email_list_id')
            <p class="form-error">{{ $message }}</p>
        @enderror

        <x-mailcoach::select-field
            :label="__('mailcoach - List')"
            name="{{ $wiremodel }}.email_list_id"
            wire:model="{{ $wiremodel }}.email_list_id"
            :options="$emailLists->pluck('name', 'id')"
            required
        />

        @if($segmentable->usingCustomSegment())
            <x-mailcoach::info>
                {{ __('mailcoach - Using custom segment') }} {{ $segmentable->getSegment()->description() }}.
            </x-mailcoach::info>
        @else
            <div class="form-field">
                @error('segment')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <label class="label label-required" for="segment">
                    {{ __('mailcoach - Segment') }}
                </label>
                <div class="radio-group">
                    <x-mailcoach::radio-field
                        name="segment"
                        option-value="entire_list"
                        wire:model="segment"
                        :label="__('mailcoach - Entire list')"
                    />
                    <div class="flex items-center">
                        <div class="flex-shrink-none">
                            <x-mailcoach::radio-field
                                name="segment"
                                wire:model="segment"
                                option-value="segment"
                                :label="__('mailcoach - Use segment')"
                            />
                        </div>
                        @if ($segment !== 'entire_list')
                            <div>
                                @php($list = \Illuminate\Support\Arr::first($segmentsData, fn(array $list) => (int) $list['id'] === (int) $segmentable->email_list_id, $segmentsData[0]))
                                @if (count($list['segments']))
                                    <div class="ml-4 -my-2">
                                        <x-mailcoach::select-field
                                            name="{{ $wiremodel }}.segment_id"
                                            wire:model="{{ $wiremodel }}.segment_id"
                                            :options="$list['segments']"
                                        />
                                        @error($wiremodel .'.segment_id')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @else
                                    <div class="ml-4">
                                        <a class="link" href="{{ $list['createSegmentUrl'] }}">{{ __('mailcoach - Create a segment first') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-mailcoach::fieldset>
