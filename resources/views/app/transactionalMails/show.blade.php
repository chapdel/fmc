@extends('mailcoach::app.transactionalMails.layouts.details', ['transactionalMail' => $transactionalMail])

@section('breadcrumbs')
    <li><span class="breadcrumb">{{ $transactionalMail->subject }}</span></li>
@endsection

@section('transactionalMail')
    here are the details
@endsection
