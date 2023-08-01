<?php

use Livewire\Livewire;

it('can update the properties of the authenticated user', function () {
    $this->authenticate();

    Livewire::test('mailcoach::profile')
        ->set('name', 'New name')
        ->set('email', 'tests@spatie.be')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertEquals('New name', auth()->user()->name);
    $this->assertEquals('tests@spatie.be', auth()->user()->email);
});
