<?php

use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Symfony\Component\Mime\Address;

beforeEach(function () {
    $this->normalizer = new AddressNormalizer();
});

it('can normalize a single address', function () {
    $results = $this->normalizer->normalize('john@example.com');

    expect($results)->toHaveCount(1);

    expect($results[0])
        ->toBeInstanceOf(Address::class)
        ->getAddress()->toBe('john@example.com')
        ->getName()->toBe('');
});

it('can normalize a multiple addresses', function () {
    $results = $this->normalizer->normalize('john@example.com, jane@example.com');

    expect($results)->toHaveCount(2);

    expect($results[0])
        ->toBeInstanceOf(Address::class)
        ->getAddress()->toBe('john@example.com')
        ->getName()->toBe('');

    expect($results[1])
        ->toBeInstanceOf(Address::class)
        ->getAddress()->toBe('jane@example.com')
        ->getName()->toBe('');
});

it('can normalize a string with email and name', function() {
    $fullString = '"John Doe" <john@example.com>';

    $results = $this->normalizer->normalize($fullString);

    expect($results)->toHaveCount(1);

    expect($results[0])
        ->toBeInstanceOf(Address::class)
        ->getAddress()->toBe('john@example.com')
        ->getName()->toBe('John Doe');
});

it('can normalize string with multiple combos of  email and name', function() {
    $fullString = '"John Doe" <john@example.com>, "Jane Doe" <jane@example.com>';

    $results = $this->normalizer->normalize($fullString);

    expect($results)->toHaveCount(2);

    expect($results[0])
        ->toBeInstanceOf(Address::class)
        ->getAddress()->toBe('john@example.com')
        ->getName()->toBe('John Doe');

    expect($results[1])
        ->toBeInstanceOf(Address::class)
        ->getAddress()->toBe('jane@example.com')
        ->getName()->toBe('Jane Doe');
});
