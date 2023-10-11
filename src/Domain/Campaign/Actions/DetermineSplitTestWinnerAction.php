<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class DetermineSplitTestWinnerAction
{
    public function execute(Campaign $campaign): Campaign
    {
        $winningContentItem = $this->getWinningContentItem($campaign);

        $campaign->update([
            'split_test_winning_content_item_id' => $winningContentItem->id,
        ]);

        return $campaign;
    }

    protected function getWinningContentItem(Campaign $campaign): ContentItem
    {
        // First check unique clicks
        $maxClickCount = $campaign->contentItems->max('unique_click_count');

        if ($maxClickCount > 0 && $campaign->contentItems->where('unique_click_count', $maxClickCount)->count() === 1) {
            return $campaign->contentItems->firstWhere('unique_click_count', $maxClickCount);
        }

        // If unique clicks are same, check opens
        $maxOpenCount = $campaign->contentItems->max('unique_open_count');

        if ($maxOpenCount > 0 && $campaign->contentItems->where('unique_open_count', $maxClickCount)->count() === 1) {
            return $campaign->contentItems->where('unique_open_count', $maxOpenCount)->first();
        }

        // If we don't have opens or clicks, we probably don't have tracking, so sort by unsubscribes instead
        return $campaign->contentItems->sortBy('unsubscribe_count')->first();
    }
}
