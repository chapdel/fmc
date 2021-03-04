---
title: Creating an automation mail
weight: 2
---

To send an email inside an automation, you must create an automation mail.

An automation mail can be created like this:

```php
AutomationMail::create()
    ->from('sender@example.com')
    ->subject('Welcome to Mailcoach')
    ->content($html)
    ->trackOpens()
    ->trackClicks();
```

The `trackOpens` and `trackClicks` calls are optional.

Alternatively, you could manually set the attributes on an `AutomationMail` model.

```php
AutomationMail::create([
   'from_email' => 'sender@example.com',
   'subject' => 'My newsletter #1',
   'content' => $html,
   'track_opens' => true,
   'track_clicks' => true,
]);
```

## Setting the content and using placeholders

You can set the content of an automation mail by setting its `HTML` attribute.

```php
$automationMail->html = $yourHtml;
$automationMail->save();
```

In that HTML you can use these placeholders which will be replaced when sending out the automation mail:

- `::unsubscribeUrl::`: this string will be replaced with the URL that, when hit, will immediately unsubscribe the person that clicked it
- `::unsubscribeTag::your tag::`: this string will be replaced with the URL that, when hit, will remove the "your tag" tag from the subscriber that clicked it
- `::webviewUrl`: this string will be replaced with the URL that will display the content of your automation mail. Learn more about this in [the docs on webviews](/docs/laravel-mailcoach/v4/automations/displaying-webviews).

If there is no way for a subscriber to unsubscribe, it will result in a lot of frustration on the part of the subscriber. We always recommend using `::unsubscribeUrl::` in the HTML of each automation mail you send.

## Setting a from name

To set a from name, just pass the name as a second parameter to `from`

```php
AutomationMail::create()->from('sender@example.com', 'Sender name')
```

## Setting a reply to

Optionally, you can set a reply to email and name like this
```php
AutomationMail::create()->replyTo('john@example.com', 'John Doe')
```
