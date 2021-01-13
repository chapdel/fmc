<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

class LinkHasher
{
    public static function hash(string $url): string
    {
        return substr(md5($url), 0, 8);
    }
}
