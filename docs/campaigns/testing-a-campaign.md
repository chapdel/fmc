---
title: Testing a campaign
weight: 3
---

Before sending a campaign to an entire list, you can send a test to a given email address.

```php
// to a single email address
$campaign->sendTestMail('john@example.com');

// to multiple email addresses at once
$campaign->sendTestMail(['john@example.com', 'paul@example.com'])
```

In the sent mail [the placeholders](/docs/laravel-mailcoach/v4/campaigns/creating-a-campaign#setting-the-content-and-using-placeholders) won't be replaced. Rest assured that when you send the mail to your entire list, they will be replaced.
