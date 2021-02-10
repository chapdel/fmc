<x-mailcoach::layout-main :title="__('Debug')">

@php($issueBody = "## Describe your issue\n\n\n\n---\n## Health check:\n\n")
<div class="form-grid">
    <x-mailcoach::fieldset :legend="__('Health')">
        @php($issueBody.='**Environment**: ' . app()->environment() . "\n")
        <dl class="dl">
            <dt>Environment</dt>
            <dd>
                <div>
                    @if (app()->environment('local'))
                        <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i> {{ app()->environment() }}
                    @else
                        <i class="far fa-check text-green-500 mr-1"></i> {{ app()->environment() }}
                    @endif
                </div>
            </dd>

            <dt>Debug</dt>
            @php($issueBody.='**Debug**: ' . (config('app.debug') ? 'ON' : 'OFF') . "\n")
            <dd>
                @if (config('app.debug'))
                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i> ON
                @else
                    <i class="far fa-check text-green-500 mr-1"></i> OFF
                @endif
            </dd>

            <dt>Horizon running</dt>
            @php($issueBody.='**Horizon**: ' . ($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE) ? 'Active' : 'Inactive') . "\n")
            <dd>
                @if($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE))
                    <i class="far fa-check text-green-500 mr-1"></i>
                @else
                    <i class="far fa-check text-red-500 mr-1"></i>
                    {!! __('<strong>Horizon</strong> is not active on your server. <a class="text-blue-500" target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                @endif
            </dd>

            <dt>Queue connection</dt>
            @php($issueBody.='**Queue** connection: ' . ($hasQueueConnection ? 'OK' : 'Not OK') . "\n")
            <dd>
                @if($hasQueueConnection)
                    <i class="far fa-check text-green-500 mr-1"></i> Queue connection settings for <code>mailcoach-redis</code> exist.
                @else
                    <i class="fas fa-times-circle text-red-500 mr-1"></i>
                    {!! __('No valid <strong>queue connection</strong> found. Configure a queue connection with the <strong>mailcoach-redis</strong> key. <a class="text-blue-500" target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                @endif
            </dd>

            <dt>Webhooks</dt>
            @php($issueBody.='**Webhooks**: ' . $webhookTableCount . " unprocessed webhooks\n")
            <dd>
                @if($webhookTableCount === 0)
                    <i class="far fa-check-circle text-green-500 mr-1"></i> No unprocessed webhooks
                @else
                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i>
                    {{ $webhookTableCount }} unprocessed webhooks
                @endif
            </dd>

            <dt>Schedule</dt>
            <dd>
                @if ($lastScheduleRun && now()->diffInMinutes($lastScheduleRun) < 10)
                    @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun) . " minute(s) ago\n")
                    <i class="far fa-check-circle text-green-500 mr-1"></i>
                    Ran {{ now()->diffInMinutes($lastScheduleRun) }} minute(s) ago
                @elseif ($lastScheduleRun)
                    @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun) . " minute(s) ago\n")
                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i>
                    Ran {{ now()->diffInMinutes($lastScheduleRun) }} minute(s) ago
                @else
                    @php($issueBody.="**Schedule**: hasn't run\n")
                    <i class="fas fa-times-circle text-red-500 mr-1"></i>
                    Schedule hasn't run
                @endif
            </dd>

            <dt>Mail config</dt>
            <dd>
                <table>
                    <tbody>
                        <tr>
                            <td class="pr-2">Default mailer:</td>
                            @php($issueBody.="**Default mailer**: " . config('mail.default') . "\n")
                            <td>
                                <span class="font-mono">{{ config('mail.default') }}</span>
                                @if (in_array(config('mail.default'), ['log', 'array', null]))
                                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="pr-2">Mailcoach mailer:</td>
                            @php($issueBody.="**Mailcoach mailer**: " . (config('mailcoach.mailer') ?? 'null') . "\n")
                            <td>
                                <span class="font-mono">{{ config('mailcoach.mailer') ?? 'null' }}</span>
                                @if (in_array(config('mailcoach.mailer'), ['log', 'array']))
                                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="pr-2">Campaign mailer:</td>
                            @php($issueBody.="**Campaign mailer**: " . (config('mailcoach.campaigns.mailer') ?? 'null') . "\n")
                            <td>
                                <span class="font-mono">{{ config('mailcoach.campaigns.mailer') ?? 'null' }}</span>
                                @if (in_array(config('mailcoach.campaigns.mailer'), ['log', 'array']))
                                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="pr-2">Transactional mailer:</td>
                            @php($issueBody.="**Transactional mailer**: " . (config('mailcoach.transactional.mailer') ?? 'null') . "\n")
                            <td>
                                <span class="font-mono">{{ config('mailcoach.transactional.mailer') ?? 'null' }}</span>
                                @if (in_array(config('mailcoach.transactional.mailer'), ['log', 'array']))
                                    <i class="far fa-exclamation-triangle text-orange-500 mr-1"></i>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </dd>
        </dl>
    </x-mailcoach::fieldset>
    <x-mailcoach::fieldset :legend="__('Details')">
        @php($issueBody.="\n\n## Technical details\n\n")
        <dl class="dl">
           
                <dt>App directory</dt>
                @php($issueBody.="**App directory**: " . base_path() . "\n")
                <dd>
                    {{ base_path() }}
                </dd>
           
                <dt>User agent</dt>
                @php($issueBody.="**User agent**: " . $_SERVER['HTTP_USER_AGENT'] . "\n")
                <dd>
                    {{ $_SERVER['HTTP_USER_AGENT'] }}
                </dd>
           
                <dt>PHP</dt>
                @php($issueBody.="**PHP version**: " . PHP_VERSION . "\n")
                <dd>
                    {{ PHP_VERSION }}
                </dd>
            
                <dt>{{ config('database.default') }}</dt>
                @php($issueBody.="**" . config('database.default') . " version**: " . $mysqlVersion . "\n")
                <dd>
                    {{ $mysqlVersion }}
                </dd>
           
                <dt>Laravel</dt>
                @php($issueBody.="**Laravel version**: " . app()->version() . "\n")
                <dd>
                    {{ app()->version() }}
                </dd>
           
                <dt>Horizon</dt>
                @php($issueBody.="**Horizon version**: " . $horizonVersion . "\n")
                <dd>
                    {{ $horizonVersion }}
                </dd>
         
                <dt>laravel-mailcoach</dt>
                @php($issueBody.="**laravel-mailcoach version**: " . $versionInfo->getCurrentVersion('laravel-mailcoach') . "\n")
                <dd>
                    {{ $versionInfo->getCurrentVersion('laravel-mailcoach') }}
                    @if(! $versionInfo->isLatest('laravel-mailcoach'))
                        <span class="font-sans text-xs inline-flex items-center bg-gray-200 bg-opacity-50 text-gray-600 rounded-sm px-1 leading-relaxed">
                            <i class="far fa-horse-head opacity-75 mr-1"></i>
                            {{ __('Upgrade available') }}
                        </span>
                    @endif
                </dd>

            @if (class_exists(\Spatie\MailcoachUi\MailcoachUiServiceProvider::class))
                <dt>mailcoach-ui</dt>
                @php($issueBody.="**mailcoach-ui version**: " . $versionInfo->getCurrentVersion('mailcoach-ui') . "\n")
                <dd>
                    {{ $versionInfo->getCurrentVersion('mailcoach-ui') }}
                    @if(! $versionInfo->isLatest('mailcoach-ui'))
                        <span class="font-sans text-xs inline-flex items-center bg-gray-200 bg-opacity-50 text-gray-600 rounded-sm px-1 leading-relaxed">
                            <i class="far fa-horse-head opacity-75 mr-1"></i>
                            {{ __('Upgrade available') }}
                        </span>
                    @endif
                </dd>
            @endif

    </x-mailcoach::fieldset>
    <x-mailcoach::fieldset  :legend="__('Having trouble?')">
        <a href="https://github.com/spatie/laravel-mailcoach/issues/new?body={{ urlencode($issueBody) }}" target="_blank">
            <x-mailcoach::button :label="__('Create a support issue')"/>
        </a>
    </x-mailcoach::fieldset>
</div>
</x-mailcoach::layout-main>
