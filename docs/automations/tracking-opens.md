---
title: Tracking opens
weight: 5
---

The package can track when and how many times a subscriber opens an automation mail.

## Enabling open tracking

To use this feature, you must set `track_opens` to `true` of an automation mail you're going to send. You can find an example of how to do this in the section on [how to create an automation mail](/docs/v4/laravel-mailcoach/automations/creating-an-automation-mail).

## How it works under the hood

When you send an automation mail that has open tracking enabled, the email service provider will add a web beacon to it..  A web beacon is an extra `img` tag in the HTML of your mail.  Its `src` attribute points to an unique endpoint on the domain of the email service provider.

Each time an email client tries to display the web beacon it will send a `get` request to email server. This way the email service provider will know the mail has been opened.

Via a web hook, the email service provider will let Mailcoach know that the mail has been opened.
