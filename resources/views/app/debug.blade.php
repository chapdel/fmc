<x-mailcoach::layout :title="__('mailcoach - Debug')">

@php($issueBody = "## Describe your issue\n\n\n\n---\n## Health check:\n\n")
<div class="card-grid form-fieldsets-no-max-w">
    <x-mailcoach::fieldset card :legend="__('mailcoach - Health')">
        <dl class="dl markup-links">
            @php($issueBody.='**Environment**: ' . app()->environment() . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!app()->environment('local')" warning="true" :label="__('mailcoach - Environment')" />
            </dt>
            <dd>
                <div>
                    {{ app()->environment() }}
                </div>
            </dd>

            @php($issueBody.='**Debug**: ' . (config('app.debug') ? 'ON' : 'OFF') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!config('app.debug')" warning="true" :label="__('mailcoach - Debug')" />
            </dt>
            <dd>
                {{ config('app.debug') ? 'ON' : 'OFF' }}
            </dd>

            @if(! $usesVapor)
                @php($issueBody.='**Horizon**: ' . ($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE) ? 'Active' : 'Inactive') . "\n")
                <dt>
                    <x-mailcoach::health-label reverse :test="$horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE)" :label="__('mailcoach - Horizon')" />
                </dt>
                <dd>
                    <p>
                    @if($horizonStatus->is(\Spatie\Mailcoach\Domain\Shared\Support\HorizonStatus::STATUS_ACTIVE))
                        {{ __('mailcoach - Active') }}
                    @else
                        {!! __('mailcoach - Horizon is inactive. <a target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                    @endif
                    </p>
                </dd>

                @php($issueBody.='**Queue** connection: ' . ($hasQueueConnection ? 'OK' : 'Not OK') . "\n")
                <dt>
                    <x-mailcoach::health-label reverse :test="$hasQueueConnection"  :label="__('mailcoach - Queue connection')" />
                </dt>
                <dd>
                    <p>
                        @if($hasQueueConnection)
                        {!! __('mailcoach - Queue connection settings for <code>mailcoach-redis</code> exist.') !!}
                        @else
                            {!! __('mailcoach - No valid <strong>queue connection</strong> found. Configure a queue connection with the <strong>mailcoach-redis</strong> key. <a target="_blank" href=":docsLink">Read the docs</a>.', ['docsLink' => 'https://mailcoach.app/docs']) !!}
                        @endif
                    </p>
                </dd>
            @endif

            @php($issueBody.='**Webhooks**: ' . $webhookTableCount . " unprocessed webhooks\n")
            <dt>
                <x-mailcoach::health-label reverse :test="$webhookTableCount === 0"  :label="__('mailcoach - Webhooks')" />
            </dt>
            <dd>
                @if($webhookTableCount === 0)
                    {{ __('mailcoach - All webhooks are processed.') }}
                @else
                    {{ __('mailcoach - :count unprocessed webhooks.', ['count' => $webhookTableCount ]) }}
                @endif
            </dd>

            <dt>
                @if ($lastScheduleRun && now()->diffInMinutes($lastScheduleRun) < 10)
                    @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun) . " minute(s) ago\n")
                    <x-mailcoach::health-label reverse :test="true"  :label="__('mailcoach - Schedule')" />
                @elseif ($lastScheduleRun)
                    @php($issueBody.='**Schedule**: ran ' . now()->diffInMinutes($lastScheduleRun) . " minute(s) ago\n")
                    <x-mailcoach::health-label reverse :test="false" warning="true" :label="__('mailcoach - Schedule')" />
                @else
                    @php($issueBody.="**Schedule**: hasn't run\n")
                    <x-mailcoach::health-label reverse :test="false" :label="__('mailcoach - Schedule')" />
                @endif
            </dt>
            <dd>
                @if ($lastScheduleRun)
                    {{ __('mailcoach - Ran :lastRun minute(s) ago.', ['lastRun' => now()->diffInMinutes($lastScheduleRun) ]) }}
                @else
                     {{ __('mailcoach - Schedule hasn\'t run.') }}
                @endif
            </dd>
            <dt>

            </dt>
            <dd>
                @if ($scheduledJobs->count())
                    <?php /** @var \Illuminate\Console\Scheduling\Event $scheduledJob */ ?>
                    <table class="table-styled">
                        <thead>
                            <th class="w-36">Schedule</th>
                            <th>Command</th>
                            <th class="w-40">Background</th>
                            <th class="w-40">No overlap</th>
                        </thead>
                        @foreach($scheduledJobs as $scheduledJob)
                            <tr>
                                <td>
                                    <code>
                                        {{ $scheduledJob->expression }}
                                    </code>
                                </td>
                                <td class="">
                                    <code>
                                        {{ \Illuminate\Support\Str::after($scheduledJob->command, '\'artisan\' ') }}
                                    </code>
                                </td>
                                <td>
                                    @if ($scheduledJob->runInBackground)
                                        <x-mailcoach::rounded-icon type="success" icon="fa-fw fas fa-check"/>
                                    @endif
                                </td>
                                <td>
                                    @if ($scheduledJob->withoutOverlapping)
                                        <x-mailcoach::rounded-icon type="success" icon="fa-fw fas fa-check"/>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    No scheduled jobs!
                @endif
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Filesystem configuration')">
        <dl class="dl">
            @foreach($filesystems as $key => $filesystem)
                @php($issueBody.="**{$key} disk**: " . $filesystem['disk'] . " (visibility: " . $filesystem['visibility'] . ")\n")
                <dt>
                    <x-mailcoach::health-label
                        :test="$filesystem['disk'] !== 'public' && $filesystem['visibility'] !== 'public'"
                        :label="$key"
                    />
                </dt>
                <dd class="block">
                    <code>
                        {{ $filesystem['disk'] }}
                    </code>
                    (visibility: {{ $filesystem['visibility'] }})
                </dd>
            @endforeach
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Mailers')">

        <dl class="dl">
            @php($issueBody.="**Default mailer**: " . config('mail.default') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mail.default'), ['log', 'array', null])" warning="true" :label="__('mailcoach - Default mailer')" />
            </dt>
            <dd>
                <code>{{ config('mail.default') }}</code>
            </dd>

            @php($issueBody.="**Mailcoach mailer**: " . (config('mailcoach.mailer') ?? 'null') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mailcoach.mailer'), ['log', 'array'])" warning="true" :label="__('mailcoach - Mailcoach mailer')" />
            </dt>
            <dd>
                <code>{{ config('mailcoach.mailer') ?? 'null' }}</code>
            </dd>

            @php($issueBody.="**Campaign mailer**: " . (config('mailcoach.campaigns.mailer') ?? 'null') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mailcoach.campaigns.mailer'), ['log', 'array'])" warning="true" :label="__('mailcoach - Campaign mailer')" />
            </dt>
            <dd>
                <code>{{ config('mailcoach.campaigns.mailer') ?? 'null' }}</code>
            </dd>

            @php($issueBody.="**Transactional mailer**: " . (config('mailcoach.transactional.mailer') ?? 'null') . "\n")
            <dt>
                <x-mailcoach::health-label reverse :test="!in_array(config('mailcoach.transactional.mailer'), ['log', 'array'])" warning="true" :label="__('mailcoach - Transactional mailer')" />
            </dt>
            <dd>
                <code>{{ config('mailcoach.transactional.mailer') ?? 'null' }}</code>
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__('mailcoach - Technical Details')">
        @php($issueBody.="\n\n## Technical details\n\n")
        <dl class="dl">
                @php($issueBody.="**App directory**: " . base_path() . "\n")
                <dt>App directory</dt>
                <dd>
                    <code>{{ base_path() }}</code>
                </dd>

                @php($issueBody.="**User agent**: " . $_SERVER['HTTP_USER_AGENT'] . "\n")
                <dt>User agent</dt>
                <dd>
                    <code>{{ $_SERVER['HTTP_USER_AGENT'] }}</code>
                </dd>

                @php($issueBody.="**PHP version**: " . PHP_VERSION . "\n")
                <dt>PHP</dt>
                <dd>
                    <code>{{ PHP_VERSION }}</code>
                </dd>

                @php($issueBody.="**" . config('database.default') . " version**: " . $mysqlVersion . "\n")
                <dt>{{ config('database.default') }}</dt>
                <dd>
                    <code>{{ $mysqlVersion }}</code>
                </dd>

                @php($issueBody.="**Laravel version**: " . app()->version() . "\n")
                <dt>Laravel</dt>
                <dd>
                    <code>{{ app()->version() }}</code>
                </dd>

                @php($issueBody.="**Horizon version**: " . $horizonVersion . "\n")
                <dt>Horizon</dt>
                <dd>
                    <code>{{ $horizonVersion }}</code>
                </dd>

                @php($issueBody.="**laravel-mailcoach version**: " . $versionInfo->getCurrentVersion('laravel-mailcoach') . "\n")
                <dt>laravel-mailcoach</dt>
                <dd>
                    <div class="flex items-center space-x-2">
                        <code>{{ $versionInfo->getCurrentVersion('laravel-mailcoach') }}</code>
                        @if(! $versionInfo->isLatest('laravel-mailcoach'))
                            <span class="font-sans text-xs inline-flex items-center bg-gray-200 bg-opacity-50 text-gray-600 rounded-sm px-1 leading-relaxed">
                                <i class="fas fa-horse-head opacity-75 mr-1"></i>
                                {{ __('mailcoach - Upgrade available') }}
                            </span>
                        @endif
                    </div>
                </dd>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card  :legend="__('mailcoach - Having trouble?')">
        <a href="https://github.com/spatie/laravel-mailcoach/issues/new?body={{ urlencode($issueBody) }}" target="_blank">
            <x-mailcoach::button :label="__('mailcoach - Create a support issue')"/>
        </a>
    </x-mailcoach::fieldset>
</div>
</x-mailcoach::layout>
