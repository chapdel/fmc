@extends('mailcoach::landingPages.layouts.landingPage', [
    'title' => __('mailcoach - Manage preferences'),
    'size' => 'max-w-lg'
])

@php($errors = new \Illuminate\Support\ViewErrorBag())

@section('landing')
    @if ($updated ?? false)
        <x-mailcoach::success>
            {{ __('mailcoach - Preferences updated successfully!') }}
        </x-mailcoach::success>
    @endif

    <p class="mt-4">
        {!! __('mailcoach - Manage your preferences for <strong class="font-semibold">:emailListName</strong>', ['emailListName' => $subscriber->emailList->name]) !!}
    </p>

    <div class="mt-4" x-data="{ unsubscribeFromAll: false }" x-init="$watch('unsubscribeFromAll', (value) => {
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
        <form method="POST">
            @foreach ($tags as $tag)
                <x-mailcoach::checkbox-field class="mb-2" name="tags[{{ $tag->name }}]" :label="$tag->name" :checked="$subscriber->hasTag($tag->name)" :errors="$errors" />
            @endforeach

            <hr class="mt-6" />

            <div class="mt-6">
                <x-mailcoach::checkbox-field class="mb-2" x-ref="all" x-model="unsubscribeFromAll" name="unsubscribe_from_all" :label="__('mailcoach - Unsubscribe from all')" :errors="$errors" />
            </div>

            @csrf
            <button class="mt-4 button bg-red-400 shadow" id="confirmationButton" type="submit">{{__('mailcoach - Save') }}</button>
        </form>
    </div>
@endsection
