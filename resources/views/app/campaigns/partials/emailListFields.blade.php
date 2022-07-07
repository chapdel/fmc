<div>
    <x-mailcoach::fieldset :legend="__('mailcoach - Audience')">
        @error('email_list_id')
            <p class="form-error">{{ $message }}</p>
        @enderror

        <div class="form-field">
            <label class="label label-required" for="email_list_id">
                {{ __('mailcoach - List') }}
            </label>
            <div class="select">
                <select name="{{ $wiremodel }}.email_list_id" id="email_list_id" wire:model="{{ $wiremodel }}.email_list_id" required>
                    <option value="">--{{ __('mailcoach - None') }}--</option>
                    @foreach($emailLists as $emailList)
                        <option value="{{ $emailList->id }}">
                            {{ $emailList->name }}
                        </option>
                    @endforeach
                </select>
                <div class="select-arrow">
                    <i class="fas fa-angle-down"></i>
                </div>
            </div>
        </div>

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
                                @foreach ($segmentsData as $list)
                                    @continue ($list['id'] !== $segmentable->email_list_id || count($list['segments']) > 0)
                                    <div class="ml-4">
                                        <a class="link" href="{{ $list['createSegmentUrl'] }}">{{ __('mailcoach - Create a segment first') }}</a>
                                    </div>
                                @endforeach
                                @php($list = \Illuminate\Support\Arr::first($segmentsData, fn(array $list) => $list['id'] === $segmentable->email_list_id, $segmentsData[0]))
                                @if (count($list['segments']))
                                    <div class="ml-4 -my-2">
                                        <div class="select">
                                            <select name="{{ $wiremodel }}.segment_id" wire:model="{{ $wiremodel }}.segment_id">
                                                <option value="">Select a segment</option>
                                                @foreach ($list['segments'] as $segment)
                                                    <option value="{{ $segment['id'] }}">{{ $segment['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <div class="select-arrow">
                                                <i class="fas fa-angle-down"></i>
                                            </div>
                                        </div>
                                        @error($wiremodel .'.segment_id')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-mailcoach::fieldset>
</div>
