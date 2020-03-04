<form class="form-grid" action="{{ route('mailcoach.emailLists.store') }}" method="POST">
    @csrf

    <c-text-field label="Name" name="name" required />
    <c-text-field label="From email" name="default_from_email" type="email" required />
    <c-text-field label="From name" name="default_from_name" />

    <div class="form-buttons">
        <button class="button">
            <c-icon-label icon="fa-address-book" text="Create list" />
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            Cancel
        </button>
    </div>
</form>
