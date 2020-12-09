<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PackageVersions\Versions;
use Spatie\Mailcoach\Support\HorizonStatus;
use Spatie\Mailcoach\Support\Version;

class DebugController
{
    public function __invoke(HorizonStatus $horizonStatus)
    {
        $versionInfo = app(Version::class);
        $hasQueueConnection = config('queue.connections.mailcoach-redis') && ! empty(config('queue.connections.mailcoach-redis'));
        $mysqlVersion = $this->mysqlVersion();
        $horizonVersion = Versions::getVersion("laravel/horizon");
        $webhookTableCount = DB::table('webhook_calls')
            ->where('name', 'like', '%-feedback')
            ->whereNull('processed_at')
            ->count();
        $lastScheduleRun = Cache::get('mailcoach-last-schedule-run');

        return view('mailcoach::app.debug', compact(
            'versionInfo',
            'horizonStatus',
            'hasQueueConnection',
            'mysqlVersion',
            'horizonVersion',
            'webhookTableCount',
            'lastScheduleRun',
        ));
    }

    private function mysqlVersion(): string
    {
        $results = DB::select('select version() as version');

        return (string) $results[0]->version;
    }
}
