@if ($replacerHelpTexts())
    <div class="markup-code alert alert-info text-sm">
        {{ __('mailcoach - You can use following placeholders in the subject and copy:') }}
        <dl class="mt-4 markup-dl">
            @foreach($replacerHelpTexts as $replacerName => $replacerDescription)
                <dt><code>::{{ $replacerName }}::</code></dt>
                <dd>{{ $replacerDescription }}</dd>
            @endforeach
        </dl>
    </div>
@endif
