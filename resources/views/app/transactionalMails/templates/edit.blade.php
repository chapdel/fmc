@extends('mailcoach::app.transactionalMails.templates.layouts.details', ['title' => $template->name])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href="{{ route('mailcoach.transactionalMails.templates') }}">
                    <span class="breadcrumb">{{ __('Templates') }}</span>
                </a>
            </li>
            <li><span class="breadcrumb">{{ $template->name }}</span></li>
        </ul>
    </nav>
@endsection

@section('main')
    <section class="card">
        <form
            class="form-grid"
            method="POST"
        >
            @csrf
            @method('PUT')

            <x-mailcoach::text-field :label="__('Name')" name="name" :value="$template->name" required />

            {!! app(config('mailcoach.campaigns.editor'))->render($template) !!}
        </form>

        <x-mailcoach::replacer-help-texts />
    </section>
@endsection
