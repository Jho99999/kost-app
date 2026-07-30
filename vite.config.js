import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // pastikan chunk besar tidak dipecah agar asset mudah di-resolve
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
