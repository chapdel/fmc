---
title: Logging transactional mails
weight: 2
---

Mailcoach can store transactional mails, record any opens and clicks, and even resend them.


## Getting started

To work with transactional mails in Mailcoach, you should use
 `Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\StoresMail` trait on your Mailable class.

## Storing mails

In the 
 `build` function of your mailable, you should call the `store` method provided by the trait.

```php
class YourMailable extends Mailable
{
    public function build()
    {
        $this
            ->store()
            ->view('mails.your-mailable')
    }
}
```

Whenever this mailable is sent, Mailcoach will store and display it in the UI.

## Tracking opens and clicks

Optionally, you can detect opens and clicks of a mailable by calling `trackOpensAndClicks`

```php
class YourMailable extends Mailable
{
    public function build()
    {
        $this
            ->trackOpensAndClicks()
            ->view('mails.your-mailable')
    }
}
```

Should you only want to track opens or clicks you can call `trackOpens` or `trackClicks`.

Tracking opens and/or clicks implies that Mailcoach will store the mail, so you don't need to call `store` separately.

## Resending stored mails

You can resend stored transactional mails via the UI or by calling `resend` on the `Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail` model

```php
TransactionalMail::find($id)->resend();
```
