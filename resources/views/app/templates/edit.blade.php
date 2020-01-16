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

            <div class="form-row max-w-full">
                <label class="label label-required" for="html">Template (HTML)</label>
                <textarea class="input input-html" required rows="20" id="html" name="html">{{ $template->html }}</textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" class="button">
                    <x-icon-label icon="fa-code" text="Save HTML" />
                </button>
            </div>
        </form>

        <x-replacer-help-texts />
    </section>
@endsection
