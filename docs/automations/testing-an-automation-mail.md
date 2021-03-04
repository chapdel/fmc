---
title: Testing an automation mail
weight: 3
---

Before sending an automation mail, you can send a test to a given email address.

```php
// to a single email address
$automationMail->sendTestMail('john@example.com');

// to multiple email addresses at once
$automationMail->sentTestMail(['john@example.com', 'paul@example.com'])
```

In the sent mail [the placeholders](/docs/v4/laravel-mailcoach/automations/creating-an-automation-mail/#setting-the-content-and-using-placeholders) won't be replaced. Rest assured that when you send the mail to your subscribers, they will be replaced.
