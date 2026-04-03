<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NewsletterSignup from '@/Components/Shared/NewsletterSignup.vue';

const year = new Date().getFullYear();
const page = usePage();
const site = computed(() => page.props.site ?? {});
</script>

<template>
    <footer class="border-t border-neutral-200 dark:border-neutral-800 mt-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-10">
            <NewsletterSignup compact />
        </div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-neutral-500 dark:text-neutral-400">
            <span>© {{ year }} {{ site.site_name || 'FSK Blog' }}</span>
            <span>{{ site.site_description || 'Laravel · Vue · Tailwind' }}</span>
        </div>
    </footer>
</template>
