import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/data-import.js',
                'resources/js/imports.js',
                'resources/js/show-audit.js'
            ],
            refresh: true,
        }),
    ],
});
