import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/techpay.js',
                'resources/js/owners.js',
                'resources/js/corporate-register.js',
            ],
            refresh: true,
        }),
    ],
});
