import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/page-editor.js',
                'resources/themes/admin/assets/css/app.css',
                'resources/themes/admin/assets/js/app.js',
                'resources/themes/frontend/assets/css/app.css',
                'resources/themes/frontend/assets/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
