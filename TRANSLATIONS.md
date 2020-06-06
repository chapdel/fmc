# Pull request

- We decided on using `{{ __('KEY') }}` due to `@trans`not working on Blade component attributes. We could use `{{ __('KEY') }}` on Blade component en `@lang` elsewere, but decided to be consistent.

# Todo
x Choice translations
X Check app/ folder
X Add all `__('')'` instances
X Check the `messages` translation
x Unlayer etc.
- Alert confirm text
X XXX was duplicated flash
- Check Str::plural
{{ trans_choice(__('There was 1 error.|There were :count errors.'), $subscriberImport->error_count) }}

## Sections
- [x] app/campaigns
- [x] app/components
- [x] app/emailLists
- [x] app/layouts
- [x] app/templates
- [x] campaign/
- [x] landingPages/
- [x] mails/

- [x] Unlayer
- [x] Monoco
