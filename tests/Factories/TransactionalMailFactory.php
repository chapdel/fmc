<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

class TransactionalMailFactory
{
    protected array $opens = [];

    protected array $clicks = [];

    protected int $count = 1;

    public static function new(): self
    {
        return new static();
    }

    public function withOpen(array $attributes = [], int $numberOfOpens = 1): self
    {
        $this->opens[] = compact('attributes', 'numberOfOpens');

        return $this;
    }

    public function withClick(array $attributes = [], int $numberOfClicks = 1): self
    {
        $this->clicks[] = compact('attributes', 'numberOfClicks');

        return $this;
    }

    public function count(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function create(array $attributes = []): TransactionalMailLogItem|Collection
    {
        $transactionalMails = TransactionalMailLogItem::factory()
            ->count($this->count)
            ->create($attributes)
            ->map(function (TransactionalMailLogItem $transactionalMail) {
                if (count($this->opens) === 0 && count($this->clicks) === 0) {
                    return $transactionalMail;
                }

                $send = Send::factory()->create([
                    'content_item_id' => $transactionalMail->contentItem->id,
                ]);

                foreach ($this->opens as $open) {
                    $openAttributes = array_merge($open['attributes'], ['send_id' => $send->id]);
                    Open::factory()->count($open['numberOfOpens'])->create($openAttributes);
                }

                foreach ($this->clicks as $click) {
                    $clickAttributes = array_merge($click['attributes'], ['send_id' => $send->id]);
                    foreach (range(1, $click['numberOfClicks']) as $i) {
                        $send->registerClick($clickAttributes['url']);
                    }
                }

                return $transactionalMail->refresh();
            });

        if ($this->count === 1) {
            return $transactionalMails->first();
        }

        return $transactionalMails;
    }
}
