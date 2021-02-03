@extends('mailcoach::app.layouts.app', ['title' => __('Transactional mail templates')])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <span class="breadcrumb">{{ __('Transactional mail templates') }}</span>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <section class="card">
        <div class="table-actions">
            <x-mailcoach::button dataModalTrigger="create-template" :label="__('Create template')"/>

            <x-mailcoach::modal :title="__('Create template')" name="create-template" :open="$errors->any()">
                @include('mailcoach::app.transactionalMails.templates.partials.create')
            </x-mailcoach::modal>

            @if($templatesCount)
                <div class="table-filters">
                    <x-mailcoach::search :placeholder="__('Filter templates…')"/>
                </div>
            @endif
        </div>

        @if($templatesCount)
            <table class="table table-fixed">
                <thead>
                <tr>
                    <x-mailcoach::th sort-by="subject">{{ __('Name') }}</x-mailcoach::th>
                    <x-mailcoach::th class="w-12" />
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td><a href="{{ route('mailcoach.transactionalMails.templates.edit', $template) }}">{{ $template->name }}</a></td>

                        <td class="td-action">
                            <div class="dropdown" data-dropdown>
                                <button class="icon-button" data-dropdown-trigger>
                                    <i class="far fa-ellipsis-v | dropdown-trigger-rotate"></i>
                                </button>
                                <ul class="dropdown-list dropdown-list-left | hidden" data-dropdown-list>
                                    <li>
                                        <x-mailcoach::form-button
                                            :action="route('mailcoach.transactionalMails.templates.duplicate', $template)"
                                        >
                                            <x-mailcoach::icon-label icon="fa-random" :text="__('Duplicate')" />
                                        </x-mailcoach::form-button>
                                    </li>
                                    <li>
                                        <x-mailcoach::form-button
                                            :action="route('mailcoach.transactionalMails.templates.delete', $template)"
                                            method="DELETE"
                                            data-confirm="true"
                                            :data-confirm-text="__('Are you sure you want to delete template :template?', ['template' => $template->name])"
                                        >
                                            <x-mailcoach::icon-label icon="fa-trash-alt" :text="__('Delete')" :caution="true" />
                                        </x-mailcoach::form-button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <x-mailcoach::table-status
                :name="__('mail|mails')"
                :paginator="$templates"
                :total-count="$templatesCount"
                :show-all-url="route('mailcoach.templates')"></x-mailcoach::table-status>
        @else
            <p class="alert alert-info">
                {!! __('You have not created any templates yet') !!}
            </p>
        @endif
    </section>
@endsection
