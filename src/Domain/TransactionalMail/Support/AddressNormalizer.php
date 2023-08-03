<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Support;

use Symfony\Component\Mime\Address;

class AddressNormalizer
{
    /**
     * @return array<int, Address>
     */
    public function normalize(?string $adresses): array
    {
        if (empty($adresses)) {
            return [];
        }

        return str($adresses)
            ->squish()
            ->explode(',')
            ->map(fn (string $address) => Address::create($address))
            ->toArray();
    }
}
