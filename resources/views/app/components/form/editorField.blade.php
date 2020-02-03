<script>
    var initialized = false;

    document.getElementById('unlayer').addEventListener('load', initUnlayer);
    document.addEventListener("turbolinks:load", initUnlayer);

    function initUnlayer() {
        if (initialized) {
            return;
        }

        unlayer.init({
            id: 'editor',
            projectId: '{{ config('mailcoach.editor.unlayer_project_id') }}',
            displayMode: 'email',
            features: {textEditor: {spellChecker: true}},
            tools: {form: {enabled: false}},
        });

        initialized = true;

        unlayer.loadDesign({!! json_encode($json) !!});

        unlayer.registerCallback('image', function(file, done) {
            var data = new FormData();
            data.append('file', file.attachments[0]);

            fetch('{{ $mediaUrl }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: data
            }).then(response => {
                // Make sure the response was valid
                if (response.status >= 200 && response.status < 300) {
                    return response
                }

                var error = new Error(response.statusText);
                error.response = response;
                throw error
            })
            .then(response => response.json())
            .then(data => done({ progress: 100, url: data.url }))
        });

        const mergeTags = {};
        @foreach ($replacerHelpTexts as $replacerName => $replacerDescription)
            mergeTags["{{ $replacerName }}"] = {
            name: "{{ $replacerName }}",
            value: "::{{ $replacerName }}::"
        };
        @endforeach

        unlayer.setMergeTags(mergeTags);

        document.getElementById('save').addEventListener('click', function (event) {
            event.preventDefault();

            unlayer.exportHtml(function(data) {
                document.getElementById('html').value = data.html;
                document.getElementById('json').value = JSON.stringify(data.design);
                document.querySelector('form').submit();
            });
        });
    }
</script>
<div class="form-row max-w-full h-full">
    @if($label ?? null)
        <label class="{{ ($required ?? false) ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}
        </label>
    @endif
    @error($name)
    <p class="form-error" role="alert">{{ $message }}</p>
    @enderror

    <div class="overflow-hidden -mx-10 h-full">
        <div id="editor" class="h-full -ml-2 pr-3 py-1" style="min-height: 75vh"></div>
    </div>
    <input type="hidden" name="html" id="html" value="{{ $html }}">
    <input type="hidden" name="json" id="json" value="{{ json_encode($json) }}">
</div>
