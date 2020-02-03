@extends('mailcoach::app.layouts.app', ['title' => $template->name])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href="{{ route('mailcoach.templates') }}">
                    <span class="breadcrumb"> Templates</span>
                </a>
            </li>
            <li><span class="breadcrumb">{{ $template->name }}</span></li>
        </ul>
    </nav>
@endsection

@section('content')
    <section class="card">
        <form
            class="form-grid"
            action="{{ route('mailcoach.templates.edit', $template) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <x-text-field label="Name" name="name" :value="$template->name" required />

            @if (config('mailcoach.editor.enabled') && ! $template->isHtmlTemplate())
                <x-editor-field
                    label="Template (HTML)"
                    name="html"
                    :html="old('html', $template->html)"
                    :json="old('json', $template->json)"
                    :media-url="route('mailcoach.templates.upload', $template)"
                ></x-editor-field>
            @else
                <x-html-field label="Template (HTML)" name="html" :value="old('html', $template->html)"></x-html-field>
            @endif

            <div class="form-buttons">
                <button id="save" type="submit" class="button">
                    <x-icon-label icon="fa-code" text="Save HTML" />
                </button>
            </div>
        </form>

        <x-replacer-help-texts />
    </section>
@endsection
