---
title: Displaying webviews
weight: 9
---

Whenever you send an automation mail, a webview is also created. A webview is a hard to guess URL that people who didn't subscribe can visit to read the content of your mail.

You can get to the URL of the webview of an automation mail:

```php
$automationMail->webViewUrl();
```

## Customizing the webview

You can customize the webview. To do this, you must publish all views:

```bash
php artisan vendor:publish --provider="Spatie\Mailcoach\MailcoachServiceProvider" --tag="mailcoach-views"
```

After that, you can customize the `webview.blade.php` view in the `resources/views/vendor/mailcoach/automation`directory.
