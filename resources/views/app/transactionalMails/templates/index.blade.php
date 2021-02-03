@extends('mailcoach::app.layouts.app', ['title' => __('Transactional mail templates')])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <span class="breadcrumb">{{ __('Transactional mail templates') }}</span>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <section class="card">
        <div class="table-actions">
            @if($templatesCount)
                <div class="table-filters">
                    <x-mailcoach::search :placeholder="__('Filter templates…')"/>
                </div>
            @endif
        </div>

        @if($templatesCount)
            <table class="table table-fixed">
                <thead>
                <tr>
                    <x-mailcoach::th sort-by="subject">{{ __('Name') }}</x-mailcoach::th>
                    <x-mailcoach::th class="w-12" />
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td><a href="{{ route('mailcoach.transactionalMails.template.edit', $template) }}">{{ $template->name }}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <x-mailcoach::table-status
                :name="__('mail|mails')"
                :paginator="$templates"
                :total-count="$templatesCount"
                :show-all-url="route('mailcoach.templates')"></x-mailcoach::table-status>
        @else
            <p class="alert alert-info">
                {!! __('You have not created any templates yet') !!}
            </p>
        @endif
    </section>
@endsection
