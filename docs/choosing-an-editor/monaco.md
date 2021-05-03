---
title: Monaco
weight: 4
---

<a href="https://microsoft.github.io/monaco-editor/">Monaco</a> is a powerful code editor created by Microsoft. It
provides code highlighting, auto completion and much more.

![screenshot](/docs/laravel-mailcoach/v4/images/editors/monaco.png)

## Configuration via the UI

When you've installed Mailcoach as a standalone app or via the 1-click-installer you can choose this editor via the UI.

On the editor configuration you can customize the looks of the Monaco editor.

![screenshot](/docs/laravel-mailcoach/v4/images/editors/monaco-config.png)

## Manual installation

When you've installed Mailcoach in an existing Laravel app, you must manually install this editor.

You can install the package via composer:

```bash
composer require spatie/laravel-mailcoach-monaco:^4.0
```

### Publish the assets

You must publish the JavaScript and CSS assets using this command:

```bash
php artisan vendor:publish --tag mailcoach-monaco-assets --force
```

Every time the package is updated you'll need to run that command. You can automate this by adding it to your `post-update-cmd` script in `composer.json`.

```
"scripts": {
    "post-update-cmd": [
        "@php artisan vendor:publish --tag mailcoach-monaco-assets --force"
    ]
}
```

### Configuring Mailcoach

Set the `mailcoach.editor` config value to `\Spatie\MailcoachMonaco\MonacoEditor::class`

### Configuring the looks

You can change some Monaco editor options by adding a `monaco` configuration key to your `mailcoach.php` config file.

```php
'monaco' => [
    'theme' => 'vs-light', // vs-light or vs-dark
    'fontFamily' => 'Jetbrains Mono',
    'fontLigatures' => true,
    'fontWeight' => 400,
    'fontSize' => '16', // No units
    'lineHeight' => '24' // No units
],
```
