<?php

use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberClickedAutomationMailLinkQueryCondition;

it('can convert to an array', function () {
    $condition = new SubscriberClickedAutomationMailLinkQueryCondition();

    expect($condition->toArray())->toBe([
        'key' => 'subscriber_clicked_automation_mail_link',
        'label' => 'Subscriber Clicked Automation Mail Link',
        'comparison_operators' => [
            'any' => 'Contains Any',
            'none' => 'Contains None',
            'equals' => 'Equals To',
            'not-equals' => 'Not Equals To',
        ],
    ]);
});
