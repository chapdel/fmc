@extends('$mailcoach::emailListWebsite.layouts.emailListWebsite', ['title' => $emailList->website_title . ' | ' . $campaign->subject)

@section('content')

    <script>
        window.customElements.define('campaign-webview', class NewsletterEmbed extends HTMLElement {
            connectedCallback() {
                const shadow = this.attachShadow({ mode: 'closed' });
                shadow.innerHTML = this.getAttribute('contents');
            }
        })
    </script>

    <div class="p-4">
        <campaign-webview contents="{{ $webview }}"></campaign-webview>
    </div>

@endsection
