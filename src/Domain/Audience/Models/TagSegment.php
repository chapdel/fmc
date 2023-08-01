<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\TagSegmentFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\ApplyConditionBuilderOnBuilderAction;
use Spatie\Mailcoach\Domain\ConditionBuilder\Collections\StoredConditionCollection;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Subscribers\SubscriberTagsQueryCondition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ComparisonOperator;
use Spatie\Mailcoach\Domain\ConditionBuilder\ValueObjects\StoredCondition;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @method static Builder|static query()
 *
 * @property StoredConditionCollection $conditions
 */
class TagSegment extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_segments';

    public $casts = [
        'all_positive_tags_required' => 'boolean',
        'all_negative_tags_required' => 'boolean',
        'stored_conditions' => StoredConditionCollection::class,
    ];

    public $guarded = [];

    public function campaigns(): HasMany
    {
        return $this->hasMany(self::getCampaignClass());
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function positiveTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_positive_segment_tags', 'segment_id', 'tag_id')
            ->orderBy('name');
    }

    public function negativeTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_negative_segment_tags', 'segment_id', 'tag_id')
            ->orderBy('name');
    }

    public function syncPositiveTags(array $tagNames, ?ComparisonOperator $operator = null): self
    {
        return $this->syncTags($tagNames, $this->positiveTags());

        $tagIds = self::getTagClass()::query()
            ->whereIn('name', $tagNames)
            ->where('email_list_id', $this->email_list_id)
            ->pluck('id')
            ->toArray();

        $this->stored_conditions->add(
            StoredCondition::make(
                key: SubscriberTagsQueryCondition::KEY,
                comparisonOperator: $operator?->value ?? ComparisonOperator::In->value,
                value: $tagIds,
            )
        );

        return $this;
    }

    public function syncNegativeTags(array $tagNames, ?ComparisonOperator $operator = null): self
    {
        return $this->syncTags($tagNames, $this->negativeTags());

        $tagIds = self::getTagClass()::query()
            ->whereIn('name', $tagNames)
            ->where('email_list_id', $this->email_list_id)
            ->pluck('id')
            ->toArray();

        $this->stored_conditions->add(
            StoredCondition::make(
                key: SubscriberTagsQueryCondition::KEY,
                comparisonOperator: $operator?->value ?? ComparisonOperator::NotIn->value,
                value: $tagIds,
            )
        );

        return $this;
    }

    protected function syncTags(array $tagNames, BelongsToMany $tagsRelation)
    {
        $tags = self::getTagClass()::query()->whereIn('name', $tagNames)->where('email_list_id', $this->email_list_id)->get();

        $tagsRelation->sync($tags);

        return $this->refresh();
    }

    /** @return Builder<Subscriber> */
    public function getSubscribersQuery(): Builder
    {
        $query = $this->emailList->subscribers()->getQuery();

        $this->applyConditionBuilder($query);

        return $query;
    }

    public function getSubscribersCount(): int
    {
        return once(function () {
            return $this->getSubscribersQuery()->count();
        });
    }

    public function applyConditionBuilder(Builder $subscribersQuery): void
    {
        app(ApplyConditionBuilderOnBuilderAction::class)->execute($subscribersQuery, $this->stored_conditions);
    }

    public function description(Campaign $campaign): string
    {
        return $this->name;
    }

    protected static function newFactory(): TagSegmentFactory
    {
        return new TagSegmentFactory();
    }
}
