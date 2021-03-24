---
title: Audience
weight: 4
---

Email lists are used to group a collection of subscribers. You can use tags and segments to further divide a list into parts that you want to target.

## Overview

![screenshot](/docs/laravel-mailcoach/v4/images/lists/index.png)

On the lists index page, you get an overview of all the email lists you can manage, with the number of active subscribers shown. If the list requires confirmation, this number only includes subscribers that have confirmed their subscription. People that have unsubscribed, or don't have a valid email address (anymore), will not be counted in the "active" subscribers amount.

## Creating a list

![screenshot](/docs/laravel-mailcoach/v4/images/lists/create.png)

When creating a new list, you must enter a name for the list, and the email and name that campaigns will be sent from. The _From email_ will be the sender for any email campaigns that target this list. This will usually be an email address that you configured while setting up your mail configuration with Mailgun / Amazon SES / SendGrid / …

The _From name_ value is used as the sender's name in received emails. If you leave this field empty, the subscribers' mail client will fill this in according to their defaults. We suggest using the name of your organization for this field.

## Subscribers

The subscribers page shows an overview of all the email addresses that have subscribed to this email list. When clicking on an email address, you can see some more of their details, and which campaigns they have already received while on this list.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/subscribers-index.png)

### Statuses

There are a couple of different statuses that a subscriber can have: _unconfirmed_, _subscribed_ and _unsubscribed_.

**Unconfirmed**

When people have signed up for your list, but have not yet confirmed their subscription, they will be _unconfirmed_. You will only see this status if the list requires confirmation.

You can manually confirm email addresses using the action menu next to their name, but we don't suggest doing this without the consent of the person this account belongs to.

**Subscribed**

These are the email addresses that will actually receive your emails.

**Unsubscribed**

If people have clicked the "unsubscribe" link in an email campaign, they will not receive any new emails from this list. Their email address will keep showing in this list until you manually remove them. You can easily remove all the unsubscribed addresses by using the action menu at the top.

You can manually revert an unsubscribe by using the action menu, but we strongly suggest not to abuse this action to respect users' privacy.

### Importing subscribers

![screenshot](/docs/laravel-mailcoach/v4/images/lists/subscribers-import.png)

You can easily import a list of subscribers into an existing list. Upload a CSV file with these columns: `email`, `first_name`, `last_name` and `tags`, and Mailcoach will start importing these into the list. When you want to attach multiple tags to a subscriber, use `;` as the delimiter.  You can follow the progress of the import, see any errors that occurred during the process, and download the uploaded file by using the action menu on the right.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/subscribers-import-page.png)

Email addresses that have the _unsubscribed_ status will not be resubscribed when running an import. Confirmation mails will not be sent out to users, even if you have enabled double opt-in for this list. Imported email addresses that had not already unsubscribed, will receive the _Confirmed_ status and will receive any subsequently sent campaigns.

Because these lists can get quite large and an import might take a while, we send you an email to inform you when Mailcoach has finished importing the subscribers:

![screenshot](/docs/laravel-mailcoach/v4/images/lists/subscribers-import-report.png)

### Exporting subscribers

You can also export the entire list or parts of it. By filtering by name, email address, status or tags, you can choose which lines will be included in an export. An export will create a CSV file with email addresses, first names, last names, and tags.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/subscribers-export.png)

## Settings

### General settings

![screenshot](/docs/laravel-mailcoach/v4/images/lists/settings-general.png)

The first field, _name_, is simply the name of this mailing list. Subscribers will see it in places like the subscription confirmation page and when unsubscribing, so make sure the name is not too technical and that it explains well what the purpose of the list is.

The _From email_ will be the sender for any email campaigns that target this list. This will usually be an email address that you configured while setting up your mail configuration with Mailgun / Amazon SES / SendGrid / …

The _From name_ value is used as the sender's name in received emails. If you leave this field empty, the subscribers' mail client will fill this in according to their defaults. We suggest using the name of your organization for this field.

You can also publish the mailing list to a public URL, to be consumed by an RSS reader. This URL will be available on your domain and is automatically generated by Mailcoach.

### Reports

You and your colleagues can receive some statistics about this list and any campaigns sent to it. Fill in all the email addresses you want to send these reports to in the "Email…" field, each separated by a comma.

- "Confirmation when a campaign gets sent to this list": Receive an email when a campaign is **sent**. If you scheduled a campaign, this report will be sent when the scheduled date is reached.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/reports-campaign-sent.png)

- "Summary of opens, clicks & bounces a day after a campaign to this list has been sent": 24 hours after sending a campaign, you will receive an email report with the opens, clicks, and bounces.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/reports-campaign-summary.png)

- "Weekly summary on the subscriber growth of this list": Every Monday morning you will receive an email report with the changes in subscribers to your list.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/reports-list-summary.png)

### Onboarding

#### Require confirmation (double opt-in)

![screenshot](/docs/laravel-mailcoach/v4/images/lists/onboarding.png)

When checking the "Require confirmation" checkbox, Mailcoach will send double opt-in emails to new subscribers. This will require them to confirm their email address before receiving any emails. By default, this email will include a link to a Mailcoach page, asking the person to confirm their subscription.

This option can be enabled to prevent spam, both to and from your mailing list. If you disable this option and have a public-facing subscription form, you can reasonably expect a lot of bots to subscribe to your mailing list, causing your mailing costs to go up, reports to be skewed and a general decline in the health of your list.

#### Allow POST from an external form

By enabling this option, you can create forms on your website that allow people to subscribe themselves to your list. Simply set the action (endpoint) of your form to the URL that was generated for this mailing list, and make sure to send at least a value for the "email" field in form submits, like so:

```html
<form
  method="POST"
  action="https://domain-where-mailcoach-runs/mailcoach/subscribe/<uuid-of-emaillist>"
>
  <div>
    <label for="email">Email</label>
    <input name="email" />
  </div>

  <div>
    <button type="submit">Subscribe</button>
  </div>
</form>
```

You can allow these subscription forms to automatically add tags to these subscribers. Each form can set different tags, so you can segment your mailing list effectively. Enter all the allowed tags in the field on the settings page, then create a hidden form field for the _tags_ value. Make sure to place it between the existing `<form></form>` tags that you created in the previous step:

```html
<form
  method="POST"
  action="https://domain-where-mailcoach-runs/mailcoach/subscribe/<uuid-of-emaillist>"
>
  <!-- other fields -->

  <input type="hidden" name="tags" value="tagA;tagB" />

  <!-- other fields and subscribe button -->
</form>
```

You can also optionally add fields for the user's first and last name:

```html
<div>
  <label for="first_name">First name</label>
  <input name="first_name" />
</div>
<div>
  <label for="last_name">Last name</label>
  <input name="last_name" />
</div>
```

You can also optionally add extra attributes fields for the user, should be allowd in List Settings:

```html
<input type="hidden" name="attribute[myfancyattribute]" value="attributeValue" />
```

Finally, Mailcoach can also redirect users after they subscribe. There are some more hidden fields for the different types of redirects, the names should be self-explanatory:

```html
<input
  type="hidden"
  name="redirect_after_subscribed"
  value="https://your-site/subscribed"
/>
<input
  type="hidden"
  name="redirect_after_already_subscribed"
  value="https://your-site/already-subscribed"
/>
<!-- only required if your list has double opt-in enabled
    <input type="hidden" name="redirect_after_subscription_pending" value="https://your-site/redirect-after-pending" />
-->
```

### Landing pages

![screenshot](/docs/laravel-mailcoach/v4/images/lists/settings-landing-pages.png)

These are the different pages that users will be sent to upon different interactions with your mailing list. We provide some default landing pages for these, but you can configure your own.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/settings-default-confirmation.png)

When creating custom landing pages, you don't need to provide any actions or buttons on these landing pages. They are simply the place users will be redirected to after performing an action on your list.

These values can be overridden by the values from the hidden redirect fields created in the previous step.

### Welcome mail

![screenshot](/docs/laravel-mailcoach/v4/images/lists/settings-welcome-mail.png)

Mailcoach can send welcome emails after people subscribe (or confirm) to your mailing list. We provide a default, but you can create your own by checking the _Send customized welcome mail_ option. In your custom mail, you can enter some placeholders, that Mailcoach will fill upon sending your email.

### Confirmation mail

![screenshot](/docs/laravel-mailcoach/v4/images/lists/settings-confirmation-mail.png)

This email will be sent to new subscribers if you have the _Require confirmation_ option enabled.

Again, you can use the default that we created for you, or you can create a custom mail and use some of the available placeholders. Make sure to include the `::confirmUrl::` in this email, so people can confirm their subscription to your mailing list. After clicking this link, they will be redirected to your _Someone subscribed_ landing page.

![screenshot](/docs/laravel-mailcoach/v4/images/lists/settings-default-confirmation-mail.png)

## Tags and segments

Tags and segments are used to divide a subscriber list into parts that can be targeted by an email campaign.

### Tags

![screenshot](/docs/laravel-mailcoach/v4/images/lists/tags.png)

Tags describe something about the user the tag is attached to.

Tags only need a name, that should describe what the tag says about a user. For example, a tag's name could be "_bought-product-A_", which would be assigned to subscribers that have bought a certain item in your (web)shop. Users can be assigned multiple tags. Tags by themselves are not used for anything in Mailcoach as long as they are not grouped into a segment.

Tags can be assigned to users manually by clicking their email address in the subscriber list, or automatically when they subscribe through a form, read about this [here](/docs/laravel-mailcoach/v4/lists/settings#subscriptions).

![screenshot](/docs/laravel-mailcoach/v4/images/lists/tags-on-subscriber.png)

### Segments

A segment is a group of tags that can be targeted by an email campaign. An example of a segment could be "_bought-any-product_" or when using negative selectors "_have-not-bought-any-product_".

![screenshot](/docs/laravel-mailcoach/v4/images/lists/segments.png)

When creating a campaign, you can choose to send it to an entire mailing list, or one of its segments:

![screenshot](/docs/laravel-mailcoach/v4/images/lists/segments-on-campaign.png)
