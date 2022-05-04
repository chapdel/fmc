<x-mailcoach::layout-main :title="__('mailcoach - Transactional templates')">
        <div class="table-actions">
            <x-mailcoach::button x-on:click="$store.modals.open('create-template')" :label="__('mailcoach - Create template')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create template')" name="create-template" :open="$errors->any()">
                <livewire:mailcoach::create-transactional-template />
            </x-mailcoach::modal>

            @if($templatesCount)
                <div class="table-filters">
                    <x-mailcoach::search :placeholder="__('mailcoach - Filter templates…')"/>
                </div>
            @endif
        </div>

        @if($templatesCount)
            <table class="table table-fixed">
                <thead>
                <tr>
                    <x-mailcoach::th sort-by="subject">{{ __('mailcoach - Name') }}</x-mailcoach::th>
                    <x-mailcoach::th class="w-12" />
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $template)
                    <tr class="markup-links">
                        <td><a href="{{ route('mailcoach.transactionalMails.templates.edit', $template) }}">{{ $template->name }}</a></td>

                        <td class="td-action">
                            <x-mailcoach::dropdown direction="left">
                                <ul>
                                    <li>
                                        <x-mailcoach::form-button
                                            :action="route('mailcoach.transactionalMails.templates.duplicate', $template)"
                                        >
                                            <x-mailcoach::icon-label icon="fas fa-random" :text="__('mailcoach - Duplicate')" />
                                        </x-mailcoach::form-button>
                                    </li>
                                    <li>
                                        <x-mailcoach::form-button
                                            :action="route('mailcoach.transactionalMails.templates.delete', $template)"
                                            method="DELETE"
                                            data-confirm="true"
                                            :data-confirm-text="__('mailcoach - Are you sure you want to delete template :template?', ['template' => $template->name])"
                                        >
                                            <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__('mailcoach - Delete')" :caution="true" />
                                        </x-mailcoach::form-button>
                                    </li>
                                </ul>
                            </x-mailcoach::dropdown>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <x-mailcoach::table-status
                :name="__('mailcoach - mail|mails')"
                :paginator="$templates"
                :total-count="$templatesCount"
                :show-all-url="route('mailcoach.templates')"></x-mailcoach::table-status>
        @else
            <x-mailcoach::help>
                {!! __('mailcoach - You have not created any templates yet.') !!}
            </x-mailcoach::help>
        @endif
    </section>
</x-mailcoach::layout-main>
