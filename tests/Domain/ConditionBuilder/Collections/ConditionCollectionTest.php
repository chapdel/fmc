<?php

use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\ConditionCollection;

it('can get options', function () {
    $conditions = ConditionCollection::allConditions()->slice(0, 1);

    expect($conditions->options())->toBe([
        [
            'value' => 'subscriber_clicked_automation_mail_link',
            'label' => 'Subscriber Clicked Automation Mail Link',
            'category' => 'actions',
        ],
    ]);
});
