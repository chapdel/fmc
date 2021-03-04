---
title: Creating custom placeholders
weight: 8
---

By default, Mailcoach offers a couple of placeholders you can use in the subject or content of your automation mail, such as `webviewUrl` and `unsubscribeUrl`.

## Creating a placeholder

Custom placeholders can be created. Do this you must create a class and let it implement `Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer` interface.

This interface contains two methods. In `replace` you must do the actual replacement. In `helpText` you must return the helptext that will be visible on the automation mail content screen.

Here is the code of the `WebviewAutomationMailReplacer` that ships with Mailcoach.

```php
namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class WebviewAutomationMailReplacer implements AutomationMailReplacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => __('This URL will display the HTML of the automation mail'),
        ];
    }

    public function replace(string $text, AutomationMail $automationMail): string
    {
        $webviewUrl = $automationMail->webviewUrl();

        return str_ireplace('::webviewUrl::', $webviewUrl, $text);
    }
}
```

After creating a replacer you must register it in the `automations.replacers` config key of the `mailcoach` config file.

## Creating a personalized replacer

A regular replacer will do its job when the automation mail is being prepared. This will only happen once when sending an automation mail. There's also a second kind of replacer: `Spatie\Mailcoach\Domain\Automation\Support\Replacers\PersonalizedReplacer`. These replacers will get executed for each mail that is being sent out in an automation mail. 
`PersonalizedReplacer`s have access to subscriber they are sent to via the `Send` object given in the `replace` method.

Here is the code of the `UnsubscribeUrlReplacer` that ships with Mailcoach.

```php
namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class UnsubscribeUrlReplacer implements PersonalizedReplacer
{
    public function helpText(): array
    {
        return [
            'unsubscribeUrl' => __('The URL where users can unsubscribe'),
            'unsubscribeTag::your tag' => __('The URL where users can be removed from a specific tag'),
        ];
    }

    public function replace(string $text, Send $pendingSend): string
    {
        $unsubscribeUrl = $pendingSend->subscriber->unsubscribeUrl($pendingSend);

        $text = str_ireplace('::unsubscribeUrl::', $unsubscribeUrl, $text);

        preg_match_all('/::unsubscribeTag::(.*)::/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            [$key, $tag] = $match;

            $unsubscribeTagUrl = $pendingSend->subscriber->unsubscribeTagUrl($tag);

            $text = str_ireplace($key, $unsubscribeTagUrl, $text);
        }

        return $text;
    }
}
```
