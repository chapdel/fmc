<x-mailcoach::layout-website :email-list="$emailList">
    <script>
        window.customElements.define('campaign-webview', class NewsletterEmbed extends HTMLElement {
            connectedCallback() {
                const shadow = this.attachShadow({mode: 'closed'});
                shadow.innerHTML = this.getAttribute('contents');
            }
        })
    </script>

    @if($emailList->show_subscription_form_on_website)
        @include('mailcoach::emailListWebsite.partials.subscription')
    @endif

    <div class="p-4">
        <campaign-webview contents="{{ $webview }}"></campaign-webview>
    </div>

</x-mailcoach::layout-website>
