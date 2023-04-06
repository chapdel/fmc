<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignLinkClicksQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public int $totalCount;

    public function __construct(CampaignLink $campaignLink, ?Request $request = null)
    {
        $prefix = DB::getTablePrefix();

        $campaignClickTable = static::getCampaignClickTableName();
        $subscriberTableName = static::getSubscriberTableName();
        $emailListTableName = static::getEmailListTableName();

        $query = static::getCampaignClickClass()::query()
            ->selectRaw("
                {$prefix}{$subscriberTableName}.uuid as subscriber_uuid,
                {$prefix}{$emailListTableName}.uuid as subscriber_email_list_uuid,
                {$prefix}{$subscriberTableName}.email as subscriber_email,
                count({$prefix}{$campaignClickTable}.subscriber_id) as click_count,
                min({$prefix}{$campaignClickTable}.created_at) AS first_clicked_at
            ")
            ->join(static::getCampaignLinkTableName(), static::getCampaignLinkTableName().'.id', '=', "{$campaignClickTable}.campaign_link_id")
            ->join($subscriberTableName, "{$subscriberTableName}.id", '=', "{$campaignClickTable}.subscriber_id")
            ->join($emailListTableName, "{$subscriberTableName}.email_list_id", '=', "{$emailListTableName}.id")
            ->where(static::getCampaignLinkTableName().'.id', $campaignLink->id);

        $this->totalCount = $query->count();

        parent::__construct($query, $request);

        $this
            ->defaultSort('-first_clicked_at')
            ->allowedSorts('email', 'click_count', 'first_clicked_at')
            ->groupBy('subscriber_uuid', 'subscriber_email_list_uuid', 'subscriber_email')
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'email'
                    )
                )
            );
    }
}
