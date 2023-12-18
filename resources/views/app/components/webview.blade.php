@if(isset($src) || isset($html))
    @php($html ??= file_get_contents($src))
    <div wire:ignore x-data="{
        html: @js($html),
    }">
        <embedded-webview-{{ md5($id ?? $html) }} x-bind:html="html" />

        @pushOnce('head')
            <script>
                class EmbeddedWebview{{ md5($id ?? $html) }} extends HTMLElement {
                    static observedAttributes = ["html"];

                    attributeChangedCallback(name, oldValue, newValue) {
                        const shadow = this.attachShadow({ mode: 'closed' });
                        shadow.innerHTML = newValue;
                    }
                }

                window.customElements.define('embedded-webview-{{ md5($id ?? $html) }}', EmbeddedWebview{{ md5($id ?? $html) }});
            </script>
        @endpushonce
    </div>
@endif
