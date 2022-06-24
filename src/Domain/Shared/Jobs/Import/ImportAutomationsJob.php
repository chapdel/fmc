<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportAutomationsJob extends ImportJob
{
    private int $index = 0;
    private int $total = 0;

    /** @var array<string, int> */
    private array $emailLists = [];

    /** @var array<int, int> */
    private array $automationMapping = [];

    /** @var array<int, int> */
    private array $automationActionMapping = [];

    public function name(): string
    {
        return 'Automations';
    }

    public function execute(): void
    {
        $automationsPath = Storage::disk(config('mailcoach.import_disk'))->path('import/automations.csv');
        $automationTriggersPath = Storage::disk(config('mailcoach.import_disk'))->path('import/automation_triggers.csv');
        $automationActionsPath = Storage::disk(config('mailcoach.import_disk'))->path('import/automation_actions.csv');

        $this->total = $this->getMeta('automations_count', 0) + $this->getMeta('automation_triggers_count', 0) + $this->getMeta('automation_actions_count', 0);
        $this->emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $this->importAutomations($automationsPath);
        $this->importAutomationTriggers($automationTriggersPath);
        $this->importAutomationActions($automationActionsPath);
    }

    private function importAutomations(string $automationsPath): void
    {
        if (! File::exists($automationsPath)) {
            return;
        }

        $reader = SimpleExcelReader::create($automationsPath);

        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $this->emailLists[$row['email_list_uuid']];
            $row['segment_id'] = self::getTagSegmentClass()::where('name', $row['segment_name'])->where('email_list_id', $row['email_list_id'])->first()?->id;
            $row['status'] = AutomationStatus::PAUSED;

            $automation = self::getAutomationClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except($row, ['id', 'email_list_uuid', 'segment_name'])),
            );
            $this->automationMapping[$row['id']] = $automation->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }

    private function importAutomationTriggers(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        foreach ($reader->getRows() as $row) {
            $row['automation_id'] = $this->automationMapping[$row['automation_id']];

            DB::table(self::getAutomationTriggerTableName())->updateOrInsert([
                'uuid' => $row['uuid'],
            ], Arr::except($row, ['id']));

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }

    private function importAutomationActions(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        foreach ($reader->getRows() as $row) {
            $row['automation_id'] = $this->automationMapping[$row['automation_id']];
            $row['parent_id'] = $row['parent_id']
                ? $this->automationActionMapping[$row['automation_id']] ?? null
                : null;

            DB::table(self::getAutomationActionTableName())->updateOrInsert([
                'uuid' => $row['uuid'],
            ], Arr::except($row, ['id']));

            $action = self::getAutomationActionClass()::where('uuid', $row['uuid'])->first();

            $this->automationActionMapping[$row['id']] = $action->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }
}
