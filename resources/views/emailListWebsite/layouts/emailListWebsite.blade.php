<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */ ?>
<!DOCTYPE html>
<html class="h-full antialiased" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <title>{{ $emailList->website_title }}</title>
    <meta name="theme-color" content="{{ $emailList->getWebsitePrimaryColor() }}">
    <meta name="description" content="{{ $emailList->website_intro }}">


    {!! \Spatie\Mailcoach\Mailcoach::styles() !!}

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if($emailList->campaigns_feed_enabled)
        <link rel="alternate" type="application/atom+xml" href="{{ route('mailcoach.feed', $emailList) }}" title="{{ $emailList->website_title }}">
    @endif
</head>
<body>
{{ $slot }}
</body>
</html>
