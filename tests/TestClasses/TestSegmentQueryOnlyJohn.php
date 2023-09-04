<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;

class TestSegmentQueryOnlyJohn extends Segment
{
    public function subscribersQuery(Builder $subscribersQuery): void
    {
        $subscribersQuery->where('email', 'john@example.com');
    }

    public function description(): string
    {
        return 'only john';
    }
}
