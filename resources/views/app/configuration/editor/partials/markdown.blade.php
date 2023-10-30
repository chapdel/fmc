<x-mailcoach::info>
    {!! __mc('The Markdown editor uses <a href=":link">EasyMDE</a> under the hood. It also offers image uploads.', ['link' => 'https://github.com/Ionaru/easy-markdown-editor']) !!}
</x-mailcoach::info>

<x-mailcoach::warning>
    {{ __mc('The Markdown editor stores content in a structured way. When switching from or to this editor, content in existing templates and draft campaigns will be lost.') }}
</x-mailcoach::warning>
