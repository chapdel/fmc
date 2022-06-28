<?php

namespace Spatie\Mailcoach\Domain\Audience\Encryption\Transformation;

use ParagonIE\CipherSweet\Contract\TransformationInterface;

class EmailSecondPart implements TransformationInterface
{
    public function __invoke(mixed $input): string
    {
        return explode('@', $input)[1] ?? '';
    }
}
