<form class="form-grid" action="{{ route('mailcoach.emailLists.subscriber.store', $emailList) }}" method="POST">
    @csrf
    <c-text-field label="Email" name="email" type="email" required />
    <c-text-field label="First name" name="first_name" />
    <c-text-field label="Last name" name="last_name" />

    <div class="form-buttons">
        <button class="button">
            <c-icon-label icon="fa-user" text="Add subscriber" />
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            Cancel
        </button>
    </div>
</form>
