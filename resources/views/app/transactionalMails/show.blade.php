@extends('mailcoach::app.transactionalMails.layouts.details', ['transactionalMail' => $transactionalMail])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $transactionalMail->subject }}</span></li>
@endsection

@section('transactionalMail')
    Subject: {{ $transactionalMail->subject }}<br />

    <x-mailcoach::mail-persons label="From" :persons="$transactionalMail->from" />
    <x-mailcoach::mail-persons label="To" :persons="$transactionalMail->to" />
    <x-mailcoach::mail-persons label="Cc" :persons="$transactionalMail->cc" />
    <x-mailcoach::mail-persons label="Bcc" :persons="$transactionalMail->bcc" />

    <iframe width="560" height="315" src="{{ route('mailcoach.transactionalMail.body', $transactionalMail) }}" />

    <h3>Opens</h3>
    <ul>
    @forelse($transactionalMail->opens as $open)
            <li>{{ $open->created_at }}</li>
    @empty
        This mail hasn't been opened yet.
    @endforelse
    </ul>

    <h3>Clicks</h3>
    <ul>
        @forelse($transactionalMail->clicks as $click)
            <li>{{ $open->created_at }}</li>
        @empty
            No links in this mail have been clicked yet.
        @endforelse
    </ul>



@endsection
