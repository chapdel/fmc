<form class="form-grid" action="{{ route('mailcoach.templates.store') }}" method="POST">
    @csrf

    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :placeholder="__('mailcoach - Newsletter template')" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create template')" />
        <x-mailcoach::button-cancel />
    </div>
</form>
