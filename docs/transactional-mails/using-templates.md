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

After you have created this model, it [will become visible in the UI](/docs/laravel-mailcoach/v4/using-mailcoach/transactional#defining-transactional-mail-templates) of Mailcoach. Users of Mailcoach will be able to change the properties of the mail.

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

You can define replacers to dynamically replace content in the template. A replacer is any class that implements the `Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer` interface. This interface requires you to implements two methods:

- `helpText`: returns the help text to be displayed in the UI
- `replace`: the function that makes the replacement.

Here's an example implementation where we will replace `::subject::` in the content of the template with the subject used on the mailable.

```php

namespace App\Support\Replacers;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class SubjectReplacer implements TransactionalMailReplacer
{
    public function helpText(): array
    {
        return [
            'subject' => 'The subject used on the template',
        ];
    }

    public function replace(string $templateText, Mailable $mailable, TransactionalMailTemplate $template): string
    {
        return str_replace('::subject::', $mailable->subject, $templateText);
    }
}
```

You should register replacers in the `transactional.replacers` config key of the `mailcoach` config file. You should use array syntax: the key is the name of your replacer, the value should be the full qualified class name

```php
// in mailcoach.php
return [
    // ...

    'transactional' => [
        /*
         * The default mailer used by Mailcoach for transactional mails.
         */
        'mailer' => null,

        /*
         * Replacers are classes that can make replacements in the body of transactional mails.
         *
         * You can use replacers to create placeholders.
         */
        'replacers' => [
            'subject' => \App\Support\Replacers\SubjectReplacer::class,
        ],
        
        // ...
    ],
];
```

To enable a registered replacer for a `TransactionalMailTemplate`, you should set the name of the replacer in the `replacers` column of the `TransactionalMailTemplate`. Use array notation to be able to specify multiple replacers.

```php
$transactionalMailTemplate->update(['replacers' => ['subject', 'another_replacer']])
```
