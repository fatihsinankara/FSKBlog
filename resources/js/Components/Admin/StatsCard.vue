<script setup>
defineProps({
    label: String,
    value: [String, Number],
    icon: Object,
    color: { type: String, default: 'indigo' },
});
</script>

<template>
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-1">{{ label }}</p>
                <p class="text-3xl font-bold text-neutral-900 dark:text-white">{{ value }}</p>
            </div>
            <div
                class="w-10 h-10 rounded-xl flex items-center justify-center"
                :class="`bg-${color}-100 dark:bg-${color}-900 text-${color}-600 dark:text-${color}-400`"
            >
                <component :is="icon" :size="20" />
            </div>
        </div>
    </div>
</template>
