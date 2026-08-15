import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/confirm-logout.js',
                'resources/js/confirm-delete.js',
                'resources/js/confirm-success.js',
                'resources/js/confirm-round.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});