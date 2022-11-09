<?php

use Spatie\Mailcoach\Domain\Settings\Commands\PublishCommand;

it('can publish the assets', function () {
    $this->artisan(PublishCommand::class)->assertExitCode(0);
});
