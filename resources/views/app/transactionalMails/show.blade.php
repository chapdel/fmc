@extends('mailcoach::app.transactionalMails.layouts.details', ['transactionalMail' => $transactionalMail])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $transactionalMail->subject }}</span></li>
@endsection

@section('transactionalMail')
    Subject: {{ $transactionalMail->subject }}<br/>

    <x-mailcoach::mail-persons label="From" :persons="$transactionalMail->from"/>
    <x-mailcoach::mail-persons label="To" :persons="$transactionalMail->to"/>
    <x-mailcoach::mail-persons label="Cc" :persons="$transactionalMail->cc"/>
    <x-mailcoach::mail-persons label="Bcc" :persons="$transactionalMail->bcc"/>

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
        @if($transactionalMail->clicksPerUrl()->count())
            <table>
                <thead>
                <tr>
                    <td>URL</td>
                    <td>Click count</td>
                    <td>First clicked at</td>
                </tr>
                </thead>
                <tbody>
                @foreach($transactionalMail->clicksPerUrl() as $clickGroup)
                    <tr>
                        <td>{{ $clickGroup['url'] }}</td>
                        <td>{{ $clickGroup['count'] }}</td>
                        <td>{{ $clickGroup['first_clicked_at'] }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        @else
            No links in this mail have been clicked yet.
        @endif

    </ul>

    <x-mailcoach::form-button action="{{ route('mailcoach.transactionalMail.resend', $transactionalMail) }}">
        Resend
    </x-mailcoach::form-button>

    <iframe width="560" height="315" src="{{ route('mailcoach.transactionalMail.body', $transactionalMail) }}"/>
@endsection
