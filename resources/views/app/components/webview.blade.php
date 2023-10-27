@if(isset($src) || isset($html))
    @php($html ??= file_get_contents($src))
    <div wire:ignore x-data="{
        html: @js($html),
    }">
            <embedded-webview x-bind:html="html" />

            <script>
                class EmbeddedWebview extends HTMLElement {
                    static observedAttributes = ["html"];

                    attributeChangedCallback(name, oldValue, newValue) {
                        const shadow = this.attachShadow({ mode: 'closed' });
                        shadow.innerHTML = newValue;
                    }
                }

                window.customElements.define('embedded-webview', EmbeddedWebview);
            </script>
    </div>
@endif
