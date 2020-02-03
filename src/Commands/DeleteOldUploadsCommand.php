<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Models\Upload;

class DeleteOldUploadsCommand extends Command
{
    public $signature = 'mailcoach:delete-old-uploads';

    public $description = 'Delete all uploads that are no longer attached to a campaign or template';

    public function handle()
    {
        $this->comment('Deleting old uploads...');

        $deletedUploadsCount = Upload::query()
            ->whereHas('templates', null, '<=', 0)
            ->whereHas('campaigns', null, '<=', 0)
            ->delete();

        $this->comment("Deleted {$deletedUploadsCount} uploads.");
    }
}
