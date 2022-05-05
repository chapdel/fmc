<div x-data="{
    segmentsData: @js($segmentsData),
    emailListId: @js(old('email_list_id', $segmentable->email_list_id) ?? $segmentsData[0]['id']),
    segment: @js(match(true) {
        $segmentable->notSegmenting() => 'entire_list',
        $segmentable->segmentingOnSubscriberTags() => 'segment',
    }),
    selectedSegment: @js(old('segment_id', $segmentable->segment_id)),
}" x-cloak>
    <x-mailcoach::fieldset :legend="__('mailcoach - Audience')">
        @error('email_list_id')
            <p class="form-error">{{ $message }}</p>
        @enderror

        <div class="form-field">
            <label class="label label-required" for="email_list_id">
                {{ __('mailcoach - List') }}
            </label>
            <div class="select">
                <select name="email_list_id" id="email_list_id" x-model="emailListId" required>
                    <option disabled value="">--{{ __('mailcoach - None') }}--</option>
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
            <x-mailcoach::help>
                {{ __('mailcoach - Using custom segment') }} {{ $segmentable->getSegment()->description() }}.
            </x-mailcoach::help>
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
                        x-model="segment"
                        :label="__('mailcoach - Entire list')"

                    />
                    <div class="flex items-center">
                        <div class="flex-shrink-none">
                            <x-mailcoach::radio-field
                                name="segment"
                                x-model="segment"
                                option-value="segment"
                                :label="__('mailcoach - Use segment')"
                            />
                        </div>
                        <div x-show="segment !== 'entire_list'">
                            <template x-for="list in segmentsData">
                                <div class="ml-4" x-show="emailListId == list.id && segmentsData.find(list => list.id == emailListId).segments.length == 0">
                                    <a class="link" :href="list.createSegmentUrl">{{ __('mailcoach - Create a segment first') }}</a>
                                </div>
                            </template>
                            <div class="ml-4 -my-2"  x-show="(segmentsData.find(list => list.id == emailListId) || segmentsData[0]).segments.length > 0">
                                @error('segment_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <div class="select">
                                    <select name="segment_id" x-model="selectedSegment">
                                        <template x-for="segment in (segmentsData.find(list => list.id == emailListId) || segmentsData[0]).segments">
                                            <option :value="segment.id" x-text="segment.name"></option>
                                        </template>
                                    </select>
                                    <div class="select-arrow">
                                        <i class="fas fa-angle-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-mailcoach::fieldset>
</div>
