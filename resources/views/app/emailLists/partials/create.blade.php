<form class="form-grid" action="{{ route('mailcoach.emailLists.store') }}" method="POST">
    @csrf

    <x-mailcoach::text-field :label="__('mailcoach - Name')"  name="name" :placeholder="__('mailcoach - Subscribers')" required />
    <x-mailcoach::text-field :label="__('mailcoach - From email')" :placeholder="auth()->user()->email" name="default_from_email" type="email" required />
    <x-mailcoach::text-field :label="__('mailcoach - From name')" :placeholder="auth()->user()->name" name="default_from_name" />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create list')" />
        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
