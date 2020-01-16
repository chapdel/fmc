<?php

namespace Spatie\Mailcoach\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static $fakeUuid = null;

    public static function bootHasUuid()
    {
        static::creating(function (Model $model) {
            $model->uuid = static::$fakeUuid ?? (string) Str::uuid();
        });
    }

    public static function findByUuid(string $uuid): ?Model
    {
        return static::where('uuid', $uuid)->first();
    }
}
