<form class="form-grid" action="{{ route('mailcoach.emailLists.store') }}" method="POST">
    @csrf

    <x-text-field :label="__('Name')"  name="name" required />
    <x-text-field :label="__('From email')" name="default_from_email" type="email" required />
    <x-text-field :label="__('From name')" name="default_from_name" />

    <div class="form-buttons">
        <button class="button">
            <x-icon-label icon="fa-address-book" :text="__('Create list')" />
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('Cancel') }}
        </button>
    </div>
</form>
