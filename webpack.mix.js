const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'resources/dist').postCss('resources/css/app.css', 'resources/dist', [
    require('postcss-easy-import')(),
    require('tailwindcss')(),
]);
