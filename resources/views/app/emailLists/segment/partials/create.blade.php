<form class="form-grid" action="{{ route('mailcoach.emailLists.segment.store', $emailList) }}" method="POST">
    @csrf
    <x-text-field :label="__('Name')" name="name" required />

    <div class="form-buttons">
        <button class="button">
            <x-icon-label icon="fa-chart-pie" :text="__('Create segment')" />
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('Cancel') }}
        </button>
    </div>
</form>
