---
title: Using templates
weight: 3
---

By defining a transactional mail template, you can let non-technical users specify the content of a transactional mail.

## Getting started

To get started you should create a `Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate` model.

```php
Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate::create([
    'name' => 'name-of-your-template',
    'type' => 'html', // this can be html, markdown or blade
    'subject' => 'The subject of your mail',
    'body' => '<html>Content of your mail</html>'
]);
```

After you have created this model, it [will become visible](TODO: add link) in the UI of Mailcoach. Users of Mailcoach will be able to change the properties of the mail.

Next, you should create a mailable and let it use the `Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate` trait. In the `build` method of your mailable you should call the `template` method.

```php
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate
use Illuminate\Mail\Mailable

class YourMailable extends Mailable
{
    use UsesMailcoachTemplate;

    public function build()
    {
        $this->template('test-template');
    }
```

When the mailable is sent, it will use the subject and content of the `TransactionalMailTemplate`.

## Using template types

You can use of one these types as the value of `type` in a `TransactionalMailTemplate` instance

- `html`: the content of the `body` column will be used as is
- `markdown`: you can use mark down in  `body` column
- `blade`: you can use Blade syntax in the `body` column. Only select this option if you trust all users of the Mailcoach UI, as arbitrary PHP in the template will be executed.

## Storing and tracking open & clicks

By default, mails using templates will not be stored or tracked.

If you want to log any mail that use the template or track opens & clicks, you should set any of these attributes on `TransactionalMailTemplate` to true:

- `store_mail`
- `track_opens`
- `track_clicks`

## Using replacers


