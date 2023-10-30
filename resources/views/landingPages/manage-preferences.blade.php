@extends('mailcoach::landingPages.layouts.landingPage', [
    'title' => __mc('Manage preferences'),
    'size' => 'max-w-lg'
])

@php($errors = new \Illuminate\Support\ViewErrorBag())

@section('landing')
    <div class="card text-xl">
        @if ($updated ?? false)
            <x-mailcoach::success>
                {{ __mc('Preferences updated successfully!') }}
            </x-mailcoach::success>
        @endif

        <p class="mt-4 mb-4 font-bold">
            {!! __mc('Manage your personal information') !!}
        </p>

        <form method="POST"
              class="flex flex-col gap-y-4"
              action="{{ action([\Spatie\Mailcoach\Http\Front\Controllers\ManagePreferencesController::class, 'updatePersonalInfo'], ['subscriberUuid' => $subscriber->uuid]) }}"
        >
            <x-mailcoach::text-field
                class="mb-2"
                label="{{ __mc('First name') }}"
                name="first_name"
                value="{{ $subscriber->first_name }}"
                :errors="$errors"
            />

            <x-mailcoach::text-field
                class="mb-2"
                label="{{ __mc('Last name') }}"
                name="last_name"
                value="{{ $subscriber->last_name }}"
                :errors="$errors"
            />

            @csrf
            <x-mailcoach::button class="mt-4" type="submit" :label="__mc('Save')" />
        </form>
    </div>

    <div class="card text-xl mt-4">
        <div x-data="{ unsubscribeFromAll: false }" x-init="$watch('unsubscribeFromAll', (value) => {
            if (value) {
                $root.querySelectorAll('input').forEach((el) => {
                    el.checked = false;
                    el.disabled = true;
                });
                $refs.all.checked = true;
                $refs.all.disabled = false;
            } else {
                $root.querySelectorAll('input').forEach((el) => {
                    el.disabled = false;
                });
                $refs.all.disabled = false;
            }
        })">
            <form method="POST"
                  class="flex flex-col gap-y-4"
                  action="{{ action([\Spatie\Mailcoach\Http\Front\Controllers\ManagePreferencesController::class, 'updateSubscriptions'], ['subscriberUuid' => $subscriber->uuid]) }}"
            >
                @csrf
                @if (count($tags))
                    <p class="mt-4 font-bold">
                        {!! __mc('Manage your subscriptions') !!}
                    </p>

                    <label class="label" for="tags">
                        {{ __mc('Preferences') }}
                    </label>

                    @foreach ($tags as $tag)
                        <x-mailcoach::checkbox-field name="tags[{{ $tag->name }}]" :label="$tag->name" :checked="$subscriber->hasTag($tag->name)" :errors="$errors" />
                    @endforeach

                    <hr>
                    <x-mailcoach::checkbox-field x-ref="all" x-model="unsubscribeFromAll" name="unsubscribe_from_all" :label="__mc('Unsubscribe from all')" :errors="$errors" />
                @else
                    <x-mailcoach::checkbox-field :hidden="true" x-ref="all" x-model="unsubscribeFromAll" name="unsubscribe_from_all" :label="__mc('Unsubscribe')" :errors="$errors" />
                @endif

                <x-mailcoach::button type="submit" :label="count($tags) ? __mc('Save') : __mc('Unsubscribe')" />
            </form>
        </div>
    </div>
@endsection
