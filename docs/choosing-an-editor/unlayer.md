---
title: Unlayer
weight: 3
---

[Unlayer](https://unlayer.com) is a beautiful wysiwyg editor, that requires no knowledge of HTML. It also offers image uploads.

![screenshot](/docs/laravel-mailcoach/v4/images/editors/unlayer.png)

## Configuration via the UI

When you've installed Mailcoach as a standalone app or via the 1-click-installer you can choose this editor via the UI on the settings screen.

## Manual installation

When you've installed Mailcoach in an existing Laravel app, you must manually install this editor.

You can install the add-on package via Composer:

```bash
composer require spatie/laravel-mailcoach-unlayer:^2.0
```

### Publish and run the migration

```bash
php artisan vendor:publish --provider="Spatie\MailcoachUnlayer\MailcoachUnlayerServiceProvider" --tag="mailcoach-unlayer-migrations"
php artisan migrate
```

### Add the route macro

You must register the routes needed to handle uploads. We recommend that you don't put this in your routes file, but in the map method of your `RouteServiceProvider`.

```php
Route::mailcoachUnlayer('mailcoachUnlayer');
```

### Configuring Mailcoach

Set the `mailcoach.editor` config value to `\Spatie\MailcoachUnlayer\UnlayerEditor::class`

### Configuring image uploads

The Mailcoach Unlayer editor supports image uploads.

To configure the `disk_name` and maximum images size, add the following configuration to your `mailcoach.php` config file.

```php
'unlayer' => [
    'disk_name' => env('MAILCOACH_UPLOAD_DISK', 'public'),
    'max_width' => 1500,
    'max_height' => 1500,
],
```

The package does not automatically delete uploads. If you upload files and replace them, the original files will still be stored on disk.

To remove unwanted upload, delete the relevant `Spatie\MediaLibrary\MediaCollections\Models\Media` models via code.

```php
Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId)->delete();
```

This way the files will also be deleted from the filesytem.

### Customizing Unlayer

Our package installs the free version of Unlayer. Should you want to customize the looks or need extra behaviour, take a look at [the pricing plans of Unlayer](https://unlayer.com/pricing).

## Switching to and from Unlayer

Unlayer editor stores content in a structured way. When switching from or to Unlayer, content in existing templates and draft campaigns will get lost.
