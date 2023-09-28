import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    build: {
        sourcemap: true,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/editors/codemirror/editor.js',
                'resources/js/editors/markdown/editor.js',
            ],
            refresh: true,
            publicDirectory: 'resources',
            buildDirectory: 'dist',
        }),
    ],
})
