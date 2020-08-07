@extends('mailcoach::app.layouts.app', ['title' => __('Debug')])

@section('header')
    <nav>
        <ul class="breadcrumbs">
            <li>
                <span class="breadcrumb">{{ __('Debug') }}</span>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
<section class="card">
    <table>
        <tbody>
            <tr>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold text-lg" colspan="2">Health</td>
            </tr>
            <tr>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Horizon</td>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                    @if($horizonStatus->is(\Spatie\Mailcoach\Support\HorizonStatus::STATUS_ACTIVE))
                        <i class="fas fa-check-circle text-green-800 mr-1"></i>
                    @else
                        <i class="fas fa-times-circle text-red-800 mr-1"></i>
                        {!! __('<strong>Horizon</strong> is not active on your server. <a class="text-blue-800" target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Queue connection</td>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                    @if($hasQueueConnection)
                        <i class="fas fa-check-circle text-green-800 mr-1"></i> Queue connection settings for <code>mailcoach-redis</code> exist.
                    @else
                        <i class="fas fa-times-circle text-red-800 mr-1"></i>
                        {!! __('No valid <strong>queue connection</strong> found. Configure a queue connection with the <strong>mailcoach-redis</strong> key. <a class="text-blue-800" target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Webhooks</td>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                    @if($webhookTableCount === 0)
                        <i class="fas fa-check-circle text-green-800 mr-1"></i> No unprocessed webhooks
                    @else
                        <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                        {{ $webhookTableCount }} unprocessed webhooks
                    @endif
                </td>
            </tr>
            <tr>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Schedule</td>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                    @if ($lastScheduleRun && now()->diffInMinutes($lastScheduleRun) < 10)
                        <i class="fas fa-check-circle text-green-800 mr-1"></i>
                        Ran {{ now()->diffInMinutes($lastScheduleRun) }} minute(s) ago
                    @elseif ($lastScheduleRun)
                        <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                        Ran {{ now()->diffInMinutes($lastScheduleRun) }} minute(s) ago
                    @else
                        <i class="fas fa-times-circle text-red-800 mr-1"></i>
                        Schedule hasn't run
                    @endif
                </td>
            </tr>
            <tr>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold align-top">Mail config</td>
                <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                    <table>
                        <tbody>

                            <tr>
                                <td class="pr-2">Default mailer:</td>
                                <td>
                                    <span class="font-mono">{{ config('mail.default') }}</span>
                                    @if (in_array(config('mail.default'), ['log', 'array', null]))
                                        <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="pr-2">Mailcoach mailer:</td>
                                <td>
                                    <span class="font-mono">{{ config('mailcoach.mailer') ?? 'null' }}</span>
                                    @if (in_array(config('mailcoach.mailer'), ['log', 'array']))
                                        <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="pr-2">Campaign mailer:</td>
                                <td>
                                    <span class="font-mono">{{ config('mailcoach.campaign_mailer') ?? 'null' }}</span>
                                    @if (in_array(config('mailcoach.campaign_mailer'), ['log', 'array']))
                                        <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="pr-2">Transactional mailer:</td>
                                <td>
                                    <span class="font-mono">{{ config('mailcoach.transactional_mailer') ?? 'null' }}</span>
                                    @if (in_array(config('mailcoach.transactional_mailer'), ['log', 'array']))
                                        <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</section>
<section class="card mt-4">
    <table>
        <tbody>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold text-lg" colspan="2">Details</td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Environment</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                {{ app()->environment() }}
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Debug</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                @if (config('app.debug'))
                    ON <i class="fas fa-exclamation-triangle text-orange-800 mr-1"></i>
                @else
                    OFF <i class="fas fa-check text-green-800 mr-1"></i>
                @endif
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">User agent</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5">
                {{ $_SERVER['HTTP_USER_AGENT'] }}
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">PHP</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-mono">
                {{ PHP_VERSION }}
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">MySQL</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-mono">
                {{ $mysqlVersion }}
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Laravel</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-mono">
                {{ app()->version() }}
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">Horizon</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-mono">
                {{ $horizonVersion }}
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">laravel-mailcoach</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-mono">
                {{ $versionInfo->getCurrentVersion('laravel-mailcoach') }}
                @if(! $versionInfo->isLatest('laravel-mailcoach'))
                    <span class="font-sans text-xs inline-flex items-center bg-green-200 text-green-800 rounded-sm px-1 leading-relaxed">
                        <i class="fas fa-horse-head opacity-50 mr-1"></i>
                        {{ __('Upgrade available') }}
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-bold">mailcoach-ui</td>
            <td class="px-2 py-4 whitespace-no-wrap text-sm leading-5 font-mono">
                {{ $versionInfo->getCurrentVersion('mailcoach-ui') }}
                @if(! $versionInfo->isLatest('mailcoach-ui'))
                    <span class="font-sans text-xs inline-flex items-center bg-green-200 text-green-800 rounded-sm px-1 leading-relaxed">
                        <i class="fas fa-horse-head opacity-50 mr-1"></i>
                        {{ __('Upgrade available') }}
                    </span>
                @endif
            </td>
        </tr>
        </tbody>
    </table>
</section>
@endsection
