<form class="form-grid" action="{{ route('mailcoach.templates.store') }}" method="POST">
    @csrf

    <x-text-field :label="__('Name')" name="name" required />

    <div class="form-buttons">
        <button class="button">
            <x-icon-label icon="fa-clipboard" :text="__('Create template')"/>
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('Cancel') }}
        </button>
    </div>
</form>
