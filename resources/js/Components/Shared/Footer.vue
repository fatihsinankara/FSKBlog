<script setup>
const year = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-neutral-200 dark:border-neutral-800 mt-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-neutral-500 dark:text-neutral-400">
            <span>© {{ year }} FSK Blog</span>
            <span>Laravel · Vue · Tailwind</span>
        </div>
    </footer>
</template>
