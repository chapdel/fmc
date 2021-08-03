<?php


namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AutomationAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class AddRandomTagAction extends AutomationAction
{
    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::tags();
    }

    public function run(Subscriber $subscriber): void
    {
        $subscriber->addTag(Str::random());
    }
}
