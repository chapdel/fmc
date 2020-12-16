@extends('mailcoach::app.layouts.app', ['title' => 'Automations'])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li><span class="breadcrumb">{{ __('Automations') }}</span></li>
        </ul>
    </nav>
@endsection

@section('content')
    <section class="card">
        <div class="table-actions">
            <button class="button" data-modal-trigger="create-automation">
                <x-icon-label icon="fa-clipboard" :text="__('Create automation')"/>
            </button>

            <x-modal :title="__('Create automation')" name="create-automation" :open="$errors->any()">
                @include('mailcoach::app.automations.partials.create')
            </x-modal>

            @if($automations->count() || $searching)
                <div class="table-filters">
                    <x-search :placeholder="__('Filter automations…')"/>
                </div>
            @endif
        </div>

        @if($automations->count())
            <table class="table table-fixed">
                <thead>
                <tr>
                    <x-th class="w-4"></x-th>
                    <x-th sort-by="name" sort-default>{{ __('Name') }}</x-th>
                    <x-th sort-by="-updated_at" class="w-48 th-numeric">{{ __('Last updated') }}</x-th>
                    <th class="w-12"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($automations as $automation)
                    <tr>
                        <td>
                            <x-form-button :action="route('mailcoach.automations.toggleStatus', $automation)">
                                @if($automation->status === \Spatie\Mailcoach\Enums\AutomationStatus::PAUSED)
                                    <span title="{{ __('Paused') }}" class="far fa-pause-circle text-orange-500"></span>
                                @else
                                    <span title="{{ __('Sent') }}" class="fas fa-sync fa-spin text-green-500"></span>
                                @endif
                            </x-form-button>
                        </td>
                        <td class="markup-links">
                            <a class="break-words" href="{{ route('mailcoach.automations.settings', $automation) }}">
                                {{ $automation->name }}
                            </a>
                        </td>
                        <td class="td-numeric">{{ $automation->updated_at->toMailcoachFormat() }}</td>
                        <td class="td-action">
                            <div class="dropdown" data-dropdown>
                                <button class="icon-button" data-dropdown-trigger>
                                    <i class="fas fa-ellipsis-v | dropdown-trigger-rotate"></i>
                                </button>
                                <ul class="dropdown-list dropdown-list-left | hidden" data-dropdown-list>
                                    <li>
                                        <x-form-button
                                            :action="route('mailcoach.automations.duplicate', $automation)"
                                        >
                                            <x-icon-label icon="fa-random" :text="__('Duplicate')" />
                                        </x-form-button>
                                    </li>
                                    <li>
                                        <x-form-button
                                            :action="route('mailcoach.automations.delete', $automation)"
                                            method="DELETE"
                                            data-confirm="true"
                                            :data-confirm-text="__('Are you sure you want to delete automation :automation?', ['automation' => $automation->name])"
                                        >
                                            <x-icon-label icon="fa-trash-alt" :text="__('Delete')" :caution="true" />
                                        </x-form-button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <x-table-status
                :name="__('automation|automations')"
                :paginator="$automations"
                :total-count="$totalAutomationsCount"
                :show-all-url="route('mailcoach.automations')"
            ></x-table-status>

        @else
            <p class="alert alert-info">
                {{ __('No automations found.') }}
            </p>
        @endif
    </section>
@endsection
