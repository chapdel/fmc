<form class="form-grid" action="{{ route('mailcoach.transactionalMails.templates.store') }}" method="POST">
    @csrf

    <x-mailcoach::text-field :label="__('Name')" name="name" :placeholder="__('Transactional mail template')" required />
@ray($errors)
    <div class="form-buttons">
        <x-mailcoach::button :label="__('Create template')" />
        <x-mailcoach::button-cancel />
    </div>
</form>
