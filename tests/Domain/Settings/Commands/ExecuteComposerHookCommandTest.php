<?php

it('can execute the composer hook', function() {
    $this->artisan('mailcoach:execute-composer-hook')->assertExitCode(0);
});
