@extends('mailcoach::app.layouts.app', ['title' => __('Transactional mails')])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <span class="breadcrumb">{{ __('Transactional mails') }}</span>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <section class="card">
        <div class="table-actions">
            @if($transactionalMailsCount)
                <div class="table-filters">
                    <x-mailcoach::search :placeholder="__('Filter transactional mails…')"/>
                </div>
            @endif
        </div>

        @if($transactionalMailsCount)
            <table class="table table-fixed">
                <thead>
                <tr>
                    <x-mailcoach::th class="w-4"></x-mailcoach::th>
                    <x-mailcoach::th sort-by="subject">{{ __('Name') }}</x-mailcoach::th>
                    <x-mailcoach::th sort-by="-sent" sort-default class="w-48 th-numeric hidden | md:table-cell">{{ __('Sent') }}</x-mailcoach::th>
                    <x-mailcoach::th class="w-12"></x-mailcoach::th>
                </tr>
                </thead>
                <tbody>
                @foreach($transactionalMails as $transactionalMail)
                    <tr>
                        <td>{{ $transactionalMail->subject }}</td>
                        <td>{{ $transactionalMail->sent_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <x-mailcoach::table-status :name="__('mail|mails')" :paginator="$transactionalMails" :total-count="$transactionalMailsCount"
                                       :show-all-url="route('mailcoach.transactionalMails')"></x-mailcoach::table-status>
        @else
                <p class="alert alert-info">
                    {!! __('No transactional mails have been sent yet!') !!}
                </p>
        @endif
    </section>
@endsection
