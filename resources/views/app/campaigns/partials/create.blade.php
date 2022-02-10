<form class="form-grid" action="{{ route('mailcoach.campaigns.store') }}" method="POST">
    @csrf

    <x-mailcoach::text-field :label="__('mailcoach - Name')" name="name" :placeholder="__('mailcoach - Newsletter #1')" required />
    <div class="form-grid" data-conditional-type="draft">
        <x-mailcoach::select-field
            :label="__('mailcoach - Email list')"
            :options="$emailListOptions"
            name="email_list_id"
            required
        />

        @if($templateOptions->count() > 1)
            <x-mailcoach::select-field
                :label="__('mailcoach - Template')"
                :options="$templateOptions"
                name="template_id"
            />
        @endif
    </div>

    <div class="form-buttons">
        <x-mailcoach::button :label="__('mailcoach - Create campaign')" />
        <x-mailcoach::button-cancel />
    </div>
</form>
