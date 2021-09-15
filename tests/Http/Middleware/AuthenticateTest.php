<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('login')->name('login');

    test()->withExceptionHandling();
});

test('when not authenticated it redirects to the login route', function () {
    test()->get(route('mailcoach.campaigns'))->assertRedirect(route('login'));
});

test('when authenticated it can view the mailcoach ui', function () {
    test()->withoutExceptionHandling();

    test()->authenticate();

    test()->get(route('mailcoach.campaigns'))->assertSuccessful();
});

it('will redirect to the login page when authenticated with the wrong guard', function () {
    config()->set('mailcoach.guard', 'api');

    test()->authenticate('web');

    test()->get(route('mailcoach.campaigns'))->assertRedirect(route('login'));
});

test('when authenticated with the right guard it can view the mailcoach ui', function () {
    config()->set('mailcoach.guard', 'api');

    test()->authenticate('api');

    test()->get(route('mailcoach.campaigns'))->assertSuccessful();
});
