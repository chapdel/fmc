# Changelog

All notable changes to `laravel-mailcoach` will be documented in this file

## 4.5.0 - 2021-06-21

- Add a view for a subscriber's custom attributes (#621)

## 4.4.6 - 2021-06-17

- Remove redundant hasTable check (#620)
- Re-add webview URL to summary page (#613)
- Removed space after translatable string + typo's (#610)
- Call toArray() instead of casting to array (#607)
- Update NL translations (#606)
- Add some missing props to the dateField blade component (#596)
- Fetch models for match from config (#595)
- Fix duplicate transactional template (#593)
- Fix incorrect model references (#591)
- Cache Event based triggers so they don't need to be queried each request

## 4.4.5 - 2021-06-14

- allow schemaless-attributes v2

## 4.4.4 - 2021-06-11

- Fix an issue where automations would keep running for unsubscribed subscribers

## 4.4.3 - 2021-05-31

- upgrade spatie/feed

## 4.4.2 - 2021-05-25

- Automation mails will now use the list from & reply to fields

## 4.4.1 - 2021-05-14

- Fix an issue with empty html throwing an error on the send page
- Fix mails rendering html

## 4.4.0 - 2021-05-12

- add `CheckLicense` command

## 4.3.4 - 2021-05-04

- fix link to docs

## 4.3.3 - 2021-04-27

## 4.3.2 - 2021-04-27

- Fix assets

## 4.3.1 - 2021-04-23

- Fixed an issue when deleting an automationMail that's inside an automation

## 4.3.0 - 2021-04-23

- Only add a `USE INDEX` statement when using MySQL
- Fix FontAwesome Free icons
- Fix an issue on the subscriber received mails view
- Added an extra Tag added trigger when subscribers are confirmed
- Optimized some queries

## 4.2.1 - 2021-04-09

- Fix a MySQL query issue with order by on the email list summary
- Adds missing table prefixes for raw queries #520

## 4.2.0 - 2021-04-07

- save extra attributes via api (#508)
- Fix LinkHasher bug when using custom Campaign model (#505)
- Use v2 of spatie/temporary-directory (#499)
- Make sure subscriber summary on email list is sorted (#516)

## 4.1.0 - 2021-04-06

- add `TransactionalMailStored` event (#507)

## 4.0.13 - 2021-03-29

- fix more mail rendering issues

## 4.0.12 - 2021-03-29

- Fix an issue where html tags were being shown in emails

## 4.0.11 - 2021-03-26

- Fix UTM tags throwing an exception on mailto, tel or other non-url urls

## 4.0.10 - 2021-03-25

- Remove some stray Ray calls

## 4.0.9 - 2021-03-24

- improved config file

## 4.0.8 - 2021-03-24

- Fix links on the automation mail statistics screen

## 4.0.7 - 2021-03-24

- Fix subscribers being added twice to the next action

## 4.0.6 - 2021-03-24

- Fix incorrect link hashes being added as tags

## 4.0.5 - 2021-03-24

- Fix stripping UTM tags from urls without query parameters

## 4.0.4 - 2021-03-24

- Fix an issue with automation counts being incorrect

## 4.0.3 - 2021-03-24

- Fix UTM tags & subscriber tags handling

## 4.0.2 - 2021-03-24

- Fix duplicating automations

## 4.0.1 - 2021-03-24

- display tags that get created for links

## 4.0.0 - 2021-03-24

- added automations
- added transactional mail log
- added transactional mail templates
- refine campaign sending
- revamp of UI
- refactor to domain oriented structure
- internal cleanup
- rewritten docs
- drop support for PHP 7

## 3.10.4 - 2021-02-19

- HTML errors should not prevent loading of links in HTML

## 3.10.3 - 2021-01-26

- Fix stray closing tag

## 3.10.2 - 2021-01-26

- Fix an issue on the campaign details when a subscriber was deleted

## 3.10.1 - 2021-01-18

- Fix welcome mail to use latest subscriber details #426

## 3.10.0 - 2021-01-15

- Allow filling the subject in the UpdateCampaignAction
- Fix an issue on the campaign details when a list was deleted

## 3.9.8 - 2021-01-08

- Fix php constraint

## 3.9.7 - 2021-01-06

- fixed an issue with large segments

## 3.9.6 - 2020-12-28

- fix for empty campaign

## 3.9.5 - 2020-12-17

- Refactor import subscribers action (#395)

## 3.9.4 - 2020-12-16

- Fix an issue with HTML loading in the delivery tab (#384)

## 3.9.3 - 2020-12-16

- trim values from import source (#392)

## 3.9.2 - 2020-12-15

- use laravel-mailcoach for support

## 3.9.1 - 2020-12-15

- improvement for large exports

## 3.9.0 - 2020-12-14

- add `Send` model to config file

## 3.8.1 - 2020-12-10

- Fix a display issue with timezones

## 3.8.0 - 2020-12-10

- Test emails are now prefixed with "[Test]"
- Test emails have a X-Entity-Ref-ID header to prevent threading
- The delivery screen now shows a warning if your message is above 102kb, which could cause clipping in Gmail
- The delivery screen now shows the links found in your campaign, so you can verify they are correct.

## 3.7.0 - 2020-12-09

- add update method on SubscribersController.php (#383)

## 3.6.5 - 2020-12-09

- Debug database version now works on postgres
- Add subject of campaign to preview modal

## 3.6.4 - 2020-12-08

- Make sure the email list graph is scoped by email list

## 3.6.3 - 2020-12-07

- Fix issue with index in multi-tenant setup

## 3.6.2 - 2020-12-07

- ensure index exists when using MySQL

## 3.6.1 - 2020-12-07

- improve number formatting

## 3.6.0 - 2020-12-07

- add list level metrics

## 3.5.0 - 2020-11-30

- add partial for tags (#375)

## 3.4.0 - 2020-11-30

- add support for PHP 8.0

## 3.3.0 - 2020-11-19

- `extra_attributes` & `tags` can now be passed to the create subscribers API endpoint.
- `tags` are now included in Subscriber responses from the API
- Campaign graph now starts from first open if there are opens while the campaign is still sending

## 3.2.13 - 2020-11-17

- fix mails being sent on default queue for campaign batch (#368)

## 3.2.12 - 2020-11-16

- fix variable name in NL translations (#367)

## 3.2.11 - 2020-11-12

- allow medialibrary v9

## 3.2.10 - 2020-11-11

- use `,` as a delimiter for `allowed_form_extra_attributes`

## 3.2.9 - 2020-10-29

- use a custom error message when sending a campaign test email

## 3.2.8 - 2020-10-28

- fix for #358

## 3.2.7 - 2020-10-28

- remove duplicate error message

## 3.2.6 - 2020-10-21

- save attributes on list (#356)

## 3.2.5 - 2020-10-21

- disable autocomplete on search inputs (#353)

## 3.2.4 - 2020-10-19

- fix open and click rates on campaign summery mail (#343)

## 3.2.3 - 2020-10-13

- fix subscription confirmation mail copy

## 3.2.2 - 2020-10-10

- translate the sent settings screen (#338)

## 3.2.1 - 2020-10-05

- fix: only send a welcome mail if the user wasn't already subscribed

## 3.2.0 - 2020-10-05

- add Dutch translations

## 3.1.3 - 2020-10-05

- fix some timezone issues
- fix <html> being added on the campaign html

## 3.1.2 - 2020-10-04

- format other numbers on campaign index view

## 3.1.1 - 2020-10-04

- format send count on campaign index screen

## 3.1.0 - 2020-10-04

- add German translations

## 3.0.6 - 2020-09-30

- improve debug page

## 3.0.5 - 2020-09-30

- pass send to unsubscribe and complaint methods

## 3.0.4 - 2020-09-29

- margin tweak on reply-to

## 3.0.3 - 2020-09-29

- improve campaign index spacing & styles

## 3.0.2 - 2020-09-29

- show segment in campaign overview

## 3.0.1 - 2020-09-29

- don't limit exception message on failed sends

## 3.0.0 - 2020-09-27

- add API
- add used configuration screen
- add reply-to email and name
- add debug page
- improved subscriber import
- use Laravel 8 queued job batches
- use class based factories
- stability improvements
- drop support for Laravel 7

## 2.23.17 - 2020-09-30

- pass send to unsubscribe and complaint methods

## 2.23.16 - 2020-09-29

- don't limit exception message on failed sends

>>>>>>> v2
## 2.23.15 - 2020-09-27

- add `$tries = 1` to `SendCampaignJob`
- add index on `campaign_id, subscriber_id` on the sends table

(see #284)

## 2.23.14 - 2020-09-24

- make sure no unserialize notices are thrown

## 2.23.13 - 2020-09-17

- detach tags when deleting subscriber

## 2.23.12 - 2020-09-08

- fix support for Laravel 8

## 2.23.11 - 2020-09-08

- add support for Laravel 8

## 2.23.10 - 2020-09-06

- allow Guzzle 7

## 2.23.9 - 2020-09-04

- fix an issue where the original doctype was not being kept

## 2.23.8 - 2020-08-28

- fix custom mailable campaign not set

## 2.23.7 - 2020-08-28

- fix extra subscriber attributes that couldn't be null

## 2.23.6 - 2020-08-25

- fix scheduling options

## 2.23.5 - 2020-08-12

- fix Carbon macro to also work on CarbonImmutable

## 2.23.4 - 2020-08-11

- fix Carbon typehints to CarbonInterface

## 2.23.3 - 2020-08-06

- require report recipients if reports are to be sent (#278)

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
