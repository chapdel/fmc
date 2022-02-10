<form class="form-grid" action="{{ route('mailcoach.emailLists.segment.store', $emailList) }}" method="POST">
    @csrf
    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" required />

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create segment')" />

        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('mailcoach - Cancel') }}
        </button>
    </div>
</form>
