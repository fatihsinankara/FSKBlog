<script setup>
import NavBar from '@/Components/Shared/NavBar.vue';
import Footer from '@/Components/Shared/Footer.vue';
import FlashMessage from '@/Components/Shared/FlashMessage.vue';
import BackToTop from '@/Components/Shared/BackToTop.vue';
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100 transition-colors duration-300">
        <NavBar />
        <FlashMessage />
        <main>
            <slot />
        </main>
        <Footer />
        <BackToTop />
    </div>
</template>
