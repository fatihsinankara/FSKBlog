<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const progress = ref(0);

function update() {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    progress.value = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
}

onMounted(() => window.addEventListener('scroll', update, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', update));
</script>

<template>
    <div class="fixed top-0 left-0 right-0 z-50 h-[2px] pointer-events-none">
        <div
            class="h-full bg-indigo-500 dark:bg-indigo-400 transition-none origin-left"
            :style="{ width: progress + '%' }"
        />
    </div>
</template>
