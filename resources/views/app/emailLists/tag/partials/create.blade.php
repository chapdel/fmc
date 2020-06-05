<form class="form-grid" action="{{ route('mailcoach.emailLists.tag.store', $emailList) }}" method="POST">
    @csrf

    <x-text-field :label="__('Name')" name="name" required />

    <div class="form-buttons">
        <button class="button">
            <x-icon-label icon="fa-tag" :text="__('Create tag')"/>
        </button>
        <button type="button" class="button-cancel" data-modal-dismiss>
            {{ __('Cancel') }}
        </button>
    </div>
</form>
