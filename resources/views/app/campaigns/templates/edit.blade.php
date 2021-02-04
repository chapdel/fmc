@extends('mailcoach::app.layouts.main', ['title' => $template->name])

@section('main')
    <h1 class="markup-h1">{{ $template->name }}</h1>
    <form
        class="form-grid"
        action="{{ route('mailcoach.templates.edit', $template) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('Name')" name="name" :value="$template->name" required />

        {!! app(config('mailcoach.campaigns.editor'))->render($template) !!}
    </form>

    <x-mailcoach::replacer-help-texts />
@endsection
