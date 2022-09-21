<?php

it('the home url will redirect to the mailcoach dashboard page when not authenticated', function () {
    $this->get('/mailcoach')->assertRedirect(route('mailcoach.dashboard'));
});
