@extends('mailcoach::landingPages.layouts.landingPage', ['title' => __('mailcoach - This endpoint requires a POST request')])

@section('landing')
    <p>
        {{ __('mailcoach - Whoops!') }}
    </p>
    <p class="mt-4">
        {{ __('mailcoach - This endpoint requires a POST request. Make sure your subscribe form is doing a POST and not a GET request.') }}
    </p>
    <p class="mt-4">
        {!! __('mailcoach - Take a look <a class="text-blue-500 underline" href=":docsUrl">at the documentation</a> for more info.', [
            'docsUrl' => 'https://spatie.be/docs/laravel-mailcoach/audience/using-subscription-forms',
        ]) !!}
    </p>
@endsection

