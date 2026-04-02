<script setup>
import { useColorMode } from '@vueuse/core';
import { Sun, Moon } from 'lucide-vue-next';

const mode = useColorMode({
    attribute: 'class',
    modes: { dark: 'dark', light: '' },
});

function toggle() {
    mode.value = mode.value === 'dark' ? 'light' : 'dark';
}
</script>

<template>
    <button
        @click="toggle"
        class="p-2 rounded-lg text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
        :aria-label="mode === 'dark' ? 'Aydınlık moda geç' : 'Karanlık moda geç'"
    >
        <Sun v-if="mode === 'dark'" :size="18" />
        <Moon v-else :size="18" />
    </button>
</template>
