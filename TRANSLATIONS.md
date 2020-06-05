# Pull request

- We decided on using `{{ __('KEY') }}` due to `@trans`not working on Blade component attributes. We could use `{{ __('KEY') }}` on Blade component en `@lang` elsewere, but decided to be consistent. We also concidered just pasing a string to the component en then translating it in the component itself, but then tools cannot scan and export all translations based from the templates.

# Decide
- Choices
