@extends('mailcoach::app.transactionalMails.templates.layouts.details', ['title' => $template->name])

@section('main')
    <section class="card">
        <form
            class="form-grid"
            method="POST"
        >
            @csrf
            @method('PUT')

           settings come here
        </form>
    </section>
@endsection
