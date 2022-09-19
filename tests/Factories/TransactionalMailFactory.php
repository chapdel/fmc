<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;

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

    public function create(array $attributes = []): TransactionalMailLogItem | Collection
    {
        $transactionalMails = TransactionalMailLogItem::factory()
            ->count($this->count)
            ->create($attributes)
            ->map(function (TransactionalMailLogItem $transactionalMail) {
                if (count($this->opens) === 0 && count($this->clicks) === 0) {
                    return $transactionalMail;
                }

                $send = Send::factory()->create([
                    'transactional_mail_log_item_id' => $transactionalMail->id,
                ]);

                foreach ($this->opens as $open) {
                    $openAttributes = array_merge($open['attributes'], ['send_id' => $send->id]);
                    TransactionalMailOpen::factory()->count($open['numberOfOpens'])->create($openAttributes);
                }

                foreach ($this->clicks as $click) {
                    $clickAttributes = array_merge($click['attributes'], ['send_id' => $send->id]);
                    TransactionalMailClick::factory()->count($click['numberOfClicks'])->create($clickAttributes);
                }

                return $transactionalMail->refresh();
            });

        if ($this->count === 1) {
            return $transactionalMails->first();
        }

        return $transactionalMails;
    }
}
