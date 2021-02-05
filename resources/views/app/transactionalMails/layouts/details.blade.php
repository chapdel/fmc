@extends('mailcoach::app.layouts.main', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $transactionalMail->subject
])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <a href="{{ route('mailcoach.transactionalMails') }}">
                    <span class="breadcrumb">{{ __('Transactional mails') }}</span>
                </a>
            </li>
            @yield('breadcrumbs')
        </ul>
    </nav>
@endsection

@section('main')
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMail.show', $transactionalMail)">
                <x-mailcoach::icon-label icon="fa-chart-area" :text="__('Details')" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    <section class="card ">
        @yield('transactionalMail')
    </section>
@endsection
