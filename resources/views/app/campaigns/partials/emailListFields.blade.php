@php use Illuminate\Support\Arr; @endphp
@props([
    'wiremodel' => null,
])
<x-mailcoach::fieldset card :legend="__mc('Audience')">
    @if($emailLists->count())
        <x-mailcoach::select-field
            :label="__mc('List')"
            name="{{ $wiremodel ? $wiremodel . '.' : '' }}email_list_id"
            wire:model.live="{{ $wiremodel ? $wiremodel . '.' : '' }}email_list_id"
            :options="$emailLists->pluck('name', 'id')"
            required
        />

        @if($segmentable->usingCustomSegment())
            <x-mailcoach::info>
                {{ __mc('Using custom segment') }} {{ $segmentable->getSegment()->description() }}.
            </x-mailcoach::info>
        @else
            <div class="form-field">
                @error('segment')
                <p class="form-error">{{ $message }}</p>
                @enderror
                <label class="label label-required" for="segment">
                    {{ __mc('Segment') }}
                </label>
                <div class="radio-group">
                    <x-mailcoach::radio-field
                            name="segment"
                            option-value="entire_list"
                            wire:model.live="segment"
                            :label="__mc('Entire list')"
                    />
                    <div class="flex items-center w-full">
                        <div class="flex-shrink-0">
                            <x-mailcoach::radio-field
                                    name="segment"
                                    wire:model.live="segment"
                                    option-value="segment"
                                    :label="__mc('Use segment')"
                            />
                        </div>
                        @if ($segment !== 'entire_list')
                            <div class="w-full">
                                @php
                                $listId = $wiremodel
                                    ? $$wiremodel->email_list_id
                                    : $email_list_id;

                                $list = Arr::first($segmentsData, fn(array $list) => (int) $list['id'] === (int) $listId, $segmentsData[0]);
                                @endphp
                                @if (count($list['segments']))
                                    <div class="ml-4 -my-2">
                                        <x-mailcoach::select-field
                                            name="{{ $wiremodel ? $wiremodel . '.' : '' }}segment_id"
                                            wire:model.live="{{ $wiremodel ? $wiremodel . '.' : '' }}segment_id"
                                            :options="$list['segments']"
                                            :placeholder="__mc('Select a segment')"
                                        />
                                        @error(($wiremodel ? $wiremodel . '.' : '') . 'segment_id')
                                        <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @else
                                    <div class="ml-4">
                                        <a class="link"
                                           href="{{ $list['createSegmentUrl'] }}">{{ __mc('Create a segment first') }}</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        <x-mailcoach::warning>
            {!! __mc('You\'ll need to create a list first. <a class="link" href=":url">Create one here</a>') !!}
        </x-mailcoach::warning>
    @endif
</x-mailcoach::fieldset>
