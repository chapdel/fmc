@extends('mailcoach::app.emailLists.layouts.segment', [
    'segment' => $segment,
    'titlePrefix' => 'Population'
])

@section('breadcrumbs')
    <li>
        <a href="{{ route('mailcoach.emailLists.segment.edit', [$segment->emailList, $segment]) }}">
            <span class="breadcrumb">{{ $segment->name }}</span>
        </a>
    </li>
    <li><span class="breadcrumb">Population</span></li>
@endsection

@section('segment')
    @if($selectedSubscribersCount)
        <div class="table-overflow">
            <table class="table table-fixed">
                <thead>
                <tr>
                    <c-th sort-by="email">Email</c-th>
                    <th>Tags</th>
                </tr>
                </thead>
                <tbody>
                @foreach($subscribers as $subscriber)
                    <tr class="markup-links">
                        <td>
                            <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$subscriber->emailList, $subscriber]) }}">
                                {{ $subscriber->email }}
                            </a>
                            <div class="td-secondary-line">
                                {{ $subscriber->first_name }} {{ $subscriber->last_name }}
                            </div>
                        </td>
                        <td>
                            @foreach($subscriber->tags()->pluck('name') as $tag)
                                <span class=tag>{{ $tag }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <c-table-status name="subscriber" :paginator="$subscribers" :total-count="$selectedSubscribersCount"
                        :show-all-url="route('mailcoach.emailLists.segment.subscribers', [$segment->emailList, $segment])">
        </c-table-status>
    @else
        <p class="alert alert-info">
            This is a very exclusive segment. Nobody got selected.
        </p>
    @endif
@endsection
