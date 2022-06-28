<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;

class TestSegmentQueryOnlyJohn extends Segment
{
    public function subscribersQuery(Builder $subscribersQuery): void
    {
        if (config('mailcoach.encryption.enabled')) {
            $firstPart = Subscriber::getEncryptedRow()->getBlindIndex('email_first_part', ['email' => 'john@example.com']);
            $subscribersQuery->where('email_idx_1', $firstPart);
            return;
        }

        $subscribersQuery->where('email', 'john@example.com');
    }

    public function description(): string
    {
        return 'only john';
    }
}
