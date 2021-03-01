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

Whenever this mailable is sent, Mailoach will store and display it in the UI.

## Tracking opens and clicks

Optionally, you can detect opens and clicks of a mailable by calling ``
