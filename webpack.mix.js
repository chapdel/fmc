const mix = require('laravel-mix');
require('laravel-mix-bundle-analyzer');

mix.js('resources/js/app.js', 'resources/dist').postCss('resources/css/app.css', 'resources/dist', [
    require('postcss-easy-import')(),
    require('tailwindcss')(),
]);

if (process.argv.includes('--analyze')) {
    mix.bundleAnalyzer();
}
