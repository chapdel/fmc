@extends('mailcoach::app.layouts.main', [
    'title' => (isset($titlePrefix) ?  $titlePrefix . ' | ' : '') . $template->name
])

@section('nav')
    <x-mailcoach::navigation :title="$template->name" :backHref="route('mailcoach.transactionalMails.templates')"
                             :backLabel="__('Templates')">
        <x-mailcoach::navigation-group icon="" title="">

            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates.edit', $template)"
                                          data-dirty-warn>
                {{ __('Content') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :href="route('mailcoach.transactionalMails.templates.settings', $template)"
                                          data-dirty-warn>
                {{ __('Settings') }}
            </x-mailcoach::navigation-item>
        </x-mailcoach::navigation-group>
    </x-mailcoach::navigation>
@endsection

@section('main')
    <section class="card ">
        @yield('transactionalMail')
    </section>
@endsection
