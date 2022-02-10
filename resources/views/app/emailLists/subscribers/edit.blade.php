<x-mailcoach::layout-subscriber :subscriber="$subscriber" :totalSendsCount="$totalSendsCount">
    <form
        class="form-grid"
        action="{{ route('mailcoach.emailLists.subscriber.details',[$subscriber->emailList, $subscriber]) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <x-mailcoach::text-field :label="__('mailcoach - Email')" name="email" :value="$subscriber->email" type="email" required />
        <x-mailcoach::text-field :label="__('mailcoach - First name')" name="first_name" :value="$subscriber->first_name" />
        <x-mailcoach::text-field :label="__('mailcoach - Last name')" name="last_name" :value="$subscriber->last_name" />
        <x-mailcoach::tags-field
            :label="__('mailcoach - Tags')"
            name="tags"
            :value="$subscriber->tags()->pluck('name')->toArray()"
            :tags="$subscriber->emailList->tags()->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::DEFAULT)->pluck('name')->toArray()"
            allow-create
        />

        <div class="form-buttons">
            <x-mailcoach::button :label="__('mailcoach - Save subscriber')" />
        </div>
    </form>
</x-mailcoach::layout-subscriber>
