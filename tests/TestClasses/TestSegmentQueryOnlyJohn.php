<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;

class TestSegmentQueryOnlyJohn extends Segment
{
    public function subscribersQuery(Builder $subscribersQuery): void
    {
        $subscribersQuery->where('email_first_5', 'john@');
    }

    public function description(): string
    {
        return 'only john';
    }
}
