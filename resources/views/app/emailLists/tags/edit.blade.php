@extends('mailcoach::app.emailLists.layouts.emailList',[
    'title' => $tag->name,
    'subTitle' => __('Tags')
])

@section('emailList')
    <form
        class="form-grid"
        action="{{ route('mailcoach.emailLists.tag.edit', [$emailList, $tag]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('Name')" name="name" :value="$tag->name" required />

        <div class="form-buttons">
            <button type="submit" class="button">
                <x-mailcoach::icon-label icon="fa-tag" :text="__('Save tag')" />
            </button>
        </div>
    </form>
@endsection
