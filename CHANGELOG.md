# Changelog

All notable changes to `laravel-mailcoach` will be documented in this file

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
