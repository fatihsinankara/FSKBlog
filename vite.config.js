import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath, URL } from 'node:url';

function isIgnoredVueUsePureAnnotationWarning(warning) {
    const details = [
        warning.message,
        warning.id,
        warning.loc?.file,
        warning.frame,
    ].filter(Boolean).join('\n');

    return warning.code === 'INVALID_ANNOTATION'
        && details.includes('node_modules/@vueuse/core/dist/index.js')
        && details.includes('#__PURE__');
}

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    build: {
        rollupOptions: {
            onwarn(warning, warn) {
                if (isIgnoredVueUsePureAnnotationWarning(warning)) {
                    return;
                }

                warn(warning);
            },
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/vue/') || id.includes('@inertiajs/vue3') || id.includes('@vueuse/core')) {
                        return 'vendor';
                    }
                    if (id.includes('lucide-vue-next')) {
                        return 'icons';
                    }
                },
            },
        },
    },
});
