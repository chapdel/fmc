---
title: Transactional
weight: 5
---

When installed into an existing Laravel app, Mailcoach can log your transactional mails. You can also define and edit templates containing the style and content of transactional mails.

## Are you a visual learner?

This video shows you a general introduction to transactional mails.

<iframe width="560" height="315" src="https://www.youtube.com/embed/cIhwpGCrhMg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

## Exploring the UI

### Logging and tracking transactional mails

When installed in a Laravel application, Mailcoach can log and display any sent mail in the UI.  It can optionally track opens and clicks of those transactional mails.

![transactional mails list](/docs/laravel-mailcoach/v4/images/transactional/index.png)

On the "Content" screen you can see the content of a mail.

![transactional mails content](/docs/laravel-mailcoach/v4/images/transactional/content.png)

If you've enabled tracking, then the opens and clicks of a mail can be seen on the "Performance" screen.

![transactional mails performance](/docs/laravel-mailcoach/v4/images/transactional/performance.png)

A transactional mail can be resent on the "Resend" screen.

![transactional mails resend](/docs/laravel-mailcoach/v4/images/transactional/resend.png)

### Defining transactional mail templates

On the "Templates" screen you can define and edit templates containing the style and content of transactional mails.

![transactional templates index](/docs/laravel-mailcoach/v4/images/transactional/templates-index.png)

On the "Content" screen you can define the content of a transactional mail. You can [define placeholders](/docs/laravel-mailcoach/v4/transactional-mails/using-templates#using-replacers) that users of the template can use.

![transactional template content](/docs/laravel-mailcoach/v4/images/transactional/template-content.png)

On the "Settings" screen of the template you can select the [format of the template](https://spatie.be/docs/laravel-mailcoach/v4/transactional-mails/using-templates#using-template-types), and specify how it should be tracked.

![transactional template settings](/docs/laravel-mailcoach/v4/images/transactional/template-content.png)

## Getting started

To be able to use this functionality, a little PHP programming experience is required. You can find the technical steps in [this section of the docs](https://spatie.be/docs/laravel-mailcoach/v4/transactional-mails/logging-transactional-mails).
