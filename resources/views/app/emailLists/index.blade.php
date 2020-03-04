@extends('mailcoach::app.layouts.app', ['title' => 'Lists'])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <span class="breadcrumb">Lists</span>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <section class="card">
        <div class="table-actions">
            <button class="button" data-modal-trigger="create-list">
                <c-icon-label icon="fa-address-book" text="Create list" />
            </button>

            <c-modal title="Create list" name="create-list" :open="$errors->any()">
                @include('mailcoach::app.emailLists.partials.create')
            </c-modal>

            @if($emailLists->count() || $searching)
                <div class="table-filters">
                    <c-search placeholder="Filter lists…"/>
                </div>
            @endif
        </div>

        @if($emailLists->count())
            <table class="table table-fixed">
                <thead>
                <tr>
                    <c-th sort-by="name" sort-default>Name</c-th>
                    <c-th sort-by="-active_subscribers_count" class="w-32 th-numeric">Active</c-th>
                    <c-th sort-by="-created_at" class="w-48 th-numeric hidden | md:table-cell">Created</c-th>
                    <th class="w-12"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($emailLists as $emailList)
                    <tr>
                        <td class="markup-links">
                            <a class="break-words" href="{{ route('mailcoach.emailLists.subscribers', $emailList) }}">
                                {{ $emailList->name }}
                            </a>
                        </td>
                        <td class="td-numeric">{{ $emailList->active_subscribers_count }}</td>
                        <td class="td-numeric hidden | md:table-cell">
                            {{ $emailList->created_at->toMailcoachFormat() }}
                        </td>
                        <td class="td-action">
                            <div class="dropdown" data-dropdown>
                                <button class="icon-button" data-dropdown-trigger>
                                    <i class="fas fa-ellipsis-v | dropdown-trigger-rotate"></i>
                                </button>
                                <ul class="dropdown-list dropdown-list-left | hidden" data-dropdown-list>
                                    <li>
                                        <c-form-button
                                            :action="route('mailcoach.emailLists.delete', $emailList)"
                                            method="DELETE"
                                            data-confirm="true"
                                        >
                                            <c-icon-label icon="fa-trash-alt" text="Delete" :caution="true" />
                                        </c-form-button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <c-table-status
                name="list"
                :paginator="$emailLists"
                :total-count="$totalEmailListsCount"
                :show-all-url="route('mailcoach.emailLists')"
            ></c-table-status>

        @else
            <p class="alert alert-info">
                @if ($searching)
                    No email lists found.
                @else
                    You'll need at least one list to gather subscribers.
                @endif
            </p>
        @endif
    </section>
@endsection
