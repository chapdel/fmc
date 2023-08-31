<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Database\Factories\SuppressionFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionReason;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionStream;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\Searchable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @method static Builder|static query()
 */
class Suppression extends Model
{
    use HasFactory;
    use HasUuid;
    use Searchable;
    use UsesMailcoachModels;

    public $table = 'mailcoach_suppressions';

    protected $guarded = [];

    public $casts = [
        'reason' => SuppressionReason::class,
        'stream' => SuppressionStream::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function getSearchableConfig(): array
    {
        return [
            'columns' => [
                self::getSuppressionClass().'.email' => 15,
            ],
        ];
    }

    protected static function newFactory(): SuppressionFactory
    {
        return new SuppressionFactory();
    }

    public static function attributesFields(): array
    {
        return [
            'email' => __('Email address'),
        ];
    }
}
