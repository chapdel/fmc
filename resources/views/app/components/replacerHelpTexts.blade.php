<div class="mt-12 markup-code alert alert-info text-sm">
    You can use following placeholders in your copy:
    <ul class="grid mt-2 gap-2">
        @foreach($replacerHelpTexts as $replacerName => $replacerDescription)
            <li><code class="mr-2">::{{ $replacerName }}::</code>{{ $replacerDescription }}</li>
        @endforeach
    </ul>
</div>
