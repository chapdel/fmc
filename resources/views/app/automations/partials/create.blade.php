<form class="form-grid" action="{{ route('mailcoach.automations.store') }}" method="POST">
    @csrf

    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :placeholder="__('mailcoach - Automation name')" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create automation')"/>
        <x-mailcoach::button-cancel :label="__('mailcoach - Cancel')"/>
    </div>
</form>
