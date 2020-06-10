@extends('mailcoach::app.emailLists.layouts.subscriber', [
    'subscriber' => $subscriber
])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $subscriber->email }}</span></li>
@endsection

@section('subscriber')
    <form
        class="form-grid"
        action="{{ route('mailcoach.emailLists.subscriber.details',[$subscriber->emailList, $subscriber]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-text-field :label="__('Email')" name="email" :value="$subscriber->email" type="email" required />
        <x-text-field :label="__('First name')" name="first_name" :value="$subscriber->first_name" />
        <x-text-field :label="__('Last name')" name="last_name" :value="$subscriber->last_name" />
        <x-tags-field
            :label="__('Tags')"
            name="tags"
            :value="$subscriber->tags()->pluck('name')->toArray()"
            :tags="$subscriber->emailList->tags()->pluck('name')->toArray()"
            allow-create
        />

        <div class="form-buttons">
            <button type="submit" class="button">
                <x-icon-label icon="fa-user" :text="__('Save')" />
            </button>
        </div>
    </form>
@endsection
