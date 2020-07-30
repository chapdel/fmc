# Changelog

All notable changes to `laravel-mailcoach` will be documented in this file

## 2.23.2 - 2020-07-30

- fix a bug with Custom Mailables with subjects that did not send

## 2.23.1 - 2020-07-28

- fix bug that prevents replacement strings from working with a custom mailable (#274)

## 2.23.0 - 2020-07-22

- use POST request to process unsubscribe (#273)

## 2.22.0 - 2020-07-22

- use model cleanup v3

## 2.21.1 - 2020-07-10

- Testing fixes for Custom Mailables

## 2.21.0 - 2020-07-10

- When subscribing an existing subscriber, tags will still be updated.

## 2.20.1 - 2020-07-01

- Fix another regression in the campaign summary

## 2.20.0 - 2020-07-01

- Add a `retry_until_hours` setting to the throttling config

## 2.19.4 - 2020-07-01

- Fix a regression where the campaign summary was not showing the correct messaging

## 2.19.3 - 2020-07-01

- Show a label in the footer when environment isn't `production` or debugging is on

## 2.19.2 - 2020-06-28

- make sure very long error messages from SMTP get processed (#268)

## 2.19.1 - 2020-06-25

- fix getting subscribers by tag (#264)

## 2.19.0 - 2020-06-25

- add config setting to register (or not) blade components (#266)

## 2.18.0 - 2020-06-25

- add German translation (#267)

## 2.17.0 - 2020-06-21

- add support for private filesystems for the import subscribers disk (#263)

## 2.16.0 - 2020-06-21

- add dutch translations (#260)

## 2.15.7 - 2020-06-18

- fix icon alignment in dropdowns (#262)

## 2.15.6 - 2020-06-18

- prevent Stripping of Email Body Element (#261)

## 2.15.5 - 2020-06-17

- translation fixes (#256)

## 2.15.4 - 2020-06-16

- fix theme

## 2.15.2 - 2020-06-14

- fix campaign sending progress bar (#251)

## 2.15.1 - 2020-06-10

- fix typo in one of the translations

## 2.15.0 - 2020-06-10

- add translations (#247)

## 2.14.2 - 2020-06-08

- fix relationship definitions to support custom models (#245)

## 2.14.1 - 2020-06-04

- use custom models in route-model binding (#244)

## 2.14.0 - 2020-06-02

- add support for custom/configurable models (#241)

## 2.13.0 - 2020-05-27

- choose the email list when creating a campaign
- fix error when viewing a campaign with a deleted segment

## 2.12.0 - 2020-05-23

- add support for defining the welcome mail job queue (#238)

## 2.11.8 - 2020-05-14

- Allow both `Carbon` and `CarbonImmutable` to be used (#235)

## 2.11.7 - 2020-05-14

- fix display of campaign summary when the list of the campaign has been deleted

## 2.11.6 - 2020-05-14

- fix for sql_mode=only_full_group_by issue
- fix campaign not being marked as sent with a custom segment

## 2.11.5 - 2020-05-07

- fix for `PrepareEmailHtmlAction` breaking html

## 2.11.4 - 2020-05-06

- fix display of custom segment classes

## 2.11.3 - 2020-05-04

- wrong route in subscribers (#231)

## 2.11.2 - 2020-04-30

- use default action is action class not set in config

## 2.11.1 - 2020-04-30

- fix all filters being active all at once on list pages

## 2.11.0 - 2020-04-30

- add `CampaignReplacer` (#226)

## 2.10.1 - 2020-04-30

- Fix Error htmlspecialchars() in delivery tab
- Fix custom segment display

## 2.10.0 - 2020-04-30

- refactor to Tailwind grid (#228)

## 2.9.1 - 2020-04-29

- fix subjects not getting replaced correctly

## 2.9.0 - 2020-04-27

- add `WebhookCallProcessedEvent` for cleaning up old webhook calls

## 2.8.0 - 2020-04-24

- make models extendible

## 2.7.4 - 2020-04-24

- allow chronos v2

## 2.7.2 - 2020-04-18

- remove links in import confirmation mail

## 2.7.1 - 2020-04-09

- make campaign on mailable nullable (#147)

## 2.7.0 - 2020-04-09

- accept time in register feedback functions

## 2.6.4 - 2020-04-08

- fix custom mailable content

## 2.6.3 - 2020-04-07

- fix broken horses image on confirmation dialog

## 2.6.2 - 2020-04-06

- fix for sending campaigns using custom mailables

## 2.6.1 - 2020-04-06

- format number of mails on confirmation dialog

## 2.6.1 - 2020-04-06

- add view `mailcoach::app.emailLists.layouts.partials.afterLastTab`

## 2.6.1 - 2020-04-06

- add ability to use replacers in the subject of a campaign

## 2.6.1 - 2020-04-06

- fix sorting on email on the outbox screen

## 2.4.6 - 2020-04-03

- fix malformed ampersands when sending

## 2.4.5 - 2020-04-03

- fix malformed ampersands in HTML validation

## 2.4.4 - 2020-04-02

- fix sorting tags by subscriber_count

## 2.4.3 - 2020-04-02

- send campaign sent confirmation only after all mails have been sent

## 2.4.2 - 2020-04-02

- fix invalid route action

## 2.4.1 - 2020-04-01

- improve modal texts

## 2.4.0 - 2020-03-30

- add duplicate segment action

## 2.3.0 - 2020-03-30

- add duplicate template action

## 2.2.2 - 2020-03-30

- improve config file comments

## 2.2.1 - 2020-03-25

- fix js asset url

## 2.2.0 - 2020-03-25

-  add `import_subscribers_disk` config option

## 2.1.3 - 2020-03-25

- version assets in blade views

## 2.1.2 - 2020-03-25

- fix icons

## 2.1.1 - 2020-03-23

- fix `ConfirmSubscriberController` not defined when using route caching

## 2.1.0 - 2020-03-22

- add `queue_connection` config option
- add `perform_on_queue.import_subscribers_job` config option

## 2.0.4 - 2020-03-20

- fix error with groupBy in `CampaignOpensQuery`

## 2.0.3 - 2020-03-13

- fix `CreateSubscriberRequest`

## 2.0.2 - 2020-03-11

- Make sure referrer is always set

## 2.0.1 - 2020-03-11

- use `booted` functions instead of `boot` in models
- fix bug where the campaign settings screen did not work when using a custom segment class

## 2.0.0 - 2020-03-10

- add support for Laravel 7
- add support for custom editors
- add ability to use multiple mail configurations
- add ability to send confirmation and welcome mails with a separate mail configuration
- add option to delay welcome mail

- drop support for Laravel 6

## 1.8.0 - 2020-02-27

- add support for Postmark

## 1.7.2 - 2020-02-21

- fix the default from name for campaigns

## 1.7.1 - 2020-02-17

- add support for instantiated segments

## 1.7.0 - 2020-02-17

- add support for instantiated segments. EDIT: due to a merging error, this functionality was not added.

## 1.6.13 - 2020-02-17

- add unique tag on `email_list_id` and `email` in the `mailcoach_subscribers` table

## 1.6.12 - 2020-02-16

- change the mail content fields to have the `text` type in the db

## 1.6.11 - 2020-02-12

- fix encoding of plain text part of sent mails

## 1.6.10 - 2020-02-11

- The `ConvertHtmlToTextAction` will now suppress errors and warnings and try to deliver plain text at all times
- Added the plain text version to the `SendTestMailAction`

## 1.6.9 - 2020-02-10

- `UpdateSubscriberRequest` will now handle lists that have a common email properly

## 1.6.8 - 2020-02-10

- fix `subscribers` view

## 1.6.7 - 2020-02-09

- prevent hidden search field when there are no search results

## 1.6.6 - 2020-02-09

- fix caching latest version

## 1.6.5 - 2020-02-09

- show exception message when html rule fails

## 1.6.4 - 2020-02-08

- fix `SendTypeFilter` file name

## 1.6.3 - 2020-02-07

- make the `url` field of the `mailcoach_campaign_links` table bigger

## 1.6.2 - 2020-02-07

- make latest version checking more robust

## 1.6.1 - 2020-02-06

- change `failure_reason` type from string to text

## 1.6.0 - 2020-02-05

- Add an X-MAILCOACH header to messages sent by Mailcoach

## 1.5.1 - 2020-02-05

- make sure the Mailcoach service provider publishes the medialibrary migration

## 1.5.0 - 2020-02-04

- add `endHead` partial

## 1.4.3 - 2020-02-03

- lower required version for package-versions to ^1.2

## 1.4.2 - 2020-02-03

- fix exception when trying to replace an attribute that is null

## 1.4.1 - 2020-02-03

- make events properties public

## 1.4.0 - 2020-02-02

- add `BounceRegisteredEvent` and `ComplaintRegisteredEvent`

## 1.3.1 - 2020-02-02

- fix `CampaignSend` query class names

## 1.3.0 - 2020-02-01

- add `middleware` config key

## 1.2.3 - 2020-02-01

- fix closing of `strong` tag in numerous views

## 1.2.2 - 2020-01-31

- send mails using default email on email list

## 1.2.1 - 2020-01-31

- fix bug in `ConfirmSubscriberController` (#16)

## 1.2.0 - 2020-01-31

- add `guard` config option

## 1.1.1 - 2020-01-29

- fix FOUC bug in Firefox

## 1.1.0 - 2020-01-29

- move factories to src, so tests of feedback packages can use them

## 1.0.0 - 2020-01-29

- initial release
