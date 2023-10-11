<?php

use Spatie\Mailcoach\Database\Factories\CampaignFactory;
use Spatie\Mailcoach\Domain\Campaign\Actions\DetermineSplitTestWinnerAction;

beforeEach(function () {
    $this->campaign = CampaignFactory::new()->create();
    $this->campaign->contentItem->replicate(['uuid'])->save();

    $this->contentItem1 = $this->campaign->contentItems->first();
    $this->contentItem2 = $this->campaign->contentItems->last();

    $this->action = app(DetermineSplitTestWinnerAction::class);
});

it('correctly determines a winner', function ($firstContentItem, $secondContentItem, $winner) {
    $this->contentItem1->update($firstContentItem);
    $this->contentItem2->update($secondContentItem);

    $this->action->execute($this->campaign);

    $winner = "contentItem{$winner}";

    expect($this->campaign->fresh()->split_test_winning_content_item_id)->toBe($this->$winner->id);
})->with([
    [
        'first' => ['unique_click_count' => 10],
        'second' => ['unique_click_count' => 11],
        'winner' => 2,
    ],
    [
        'first' => ['unique_click_count' => 10],
        'second' => ['unique_click_count' => 10],
        'winner' => 1,
    ],
    [
        'first' => ['unique_click_count' => 10, 'unique_open_count' => 10],
        'second' => ['unique_click_count' => 10, 'unique_open_count' => 11],
        'winner' => 2,
    ],
    [
        'first' => ['unique_click_count' => 10, 'unique_open_count' => 10, 'unsubscribe_count' => 2],
        'second' => ['unique_click_count' => 10, 'unique_open_count' => 10, 'unsubscribe_count' => 1],
        'winner' => 2,
    ],
    [
        'first' => ['unique_click_count' => 10, 'unsubscribe_count' => 2],
        'second' => ['unique_click_count' => 10, 'unsubscribe_count' => 1],
        'winner' => 2,
    ],
]);
