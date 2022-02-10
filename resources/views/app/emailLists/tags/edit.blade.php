<x-mailcoach::layout-list
    :title="$tag->name"
    :originTitle="__('mailcoach - Tags')"
    :originHref="route('mailcoach.emailLists.tags', ['emailList' => $emailList])"
    :emailList="$emailList"
>
    <form
        class="form-grid"
        action="{{ route('mailcoach.emailLists.tag.edit', [$emailList, $tag]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :value="$tag->name" required />

        <div class="form-buttons">
            <x-mailcoach::button :label="__('mailcoach - Save tag')" />
        </div>
    </form>
</x-mailcoach::layout-list>
