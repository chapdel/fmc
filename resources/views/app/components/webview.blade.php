@if(isset($src) || isset($html))
    @php($uuid = \Illuminate\Support\Str::uuid())
    @php($key = \Illuminate\Support\Str::random())

    <embedded-webview-{{ $uuid }} @isset($src) src="{{ $src }}" @endisset>
    </embedded-webview-{{ $uuid }}>

    @isset($html)
    <script>
        class EmbeddedWebview{{ $key }} extends HTMLElement {
            connectedCallback() {
                const shadow = this.attachShadow({ mode: 'closed' });
                shadow.innerHTML = @js(str_contains($html, '<html') ? $html : "<pre>{$html}</pre>");
            }
        }
    </script>
    @else
        <script>
            class EmbeddedWebview{{ $key }} extends HTMLElement {
                connectedCallback() {
                    fetch(this.getAttribute('src'))
                    .then(response => response.text())
                    .then(html => {
                        const shadow = this.attachShadow({ mode: 'closed' });
                        shadow.innerHTML = html;
                    });
                }
            }
        </script>
    @endif
    <script>
        window.customElements.define('embedded-webview-{{ $uuid }}', EmbeddedWebview{{ $key }});
    </script>
@endif
