<form class="form-grid" action="{{ route('mailcoach.emailLists.segment.store', $emailList) }}" method="POST">
    @csrf
    <c-text-field label="Name" name="name" required />

    <div class="form-buttons">
        <button class="button">
            <c-icon-label icon="fa-chart-pie" text="Create segment" />
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            Cancel
        </button>
    </div>
</form>
