<form class="form-grid" action="{{ route('mailcoach.emailLists.subscriber.store', $emailList) }}" method="POST">
    @csrf
    <x-text-field :label="__('Email')" name="email" type="email" required />
    <x-text-field :label="__('First name')" name="first_name" />
    <x-text-field :label="__('Last name')" name="last_name" />

    <div class="form-buttons">
        <button class="button">
            <x-icon-label icon="fa-user" :text="__('Add subscriber')" />
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('Cancel') }}
        </button>
    </div>
</form>
