<script setup>
import { AlertTriangle } from 'lucide-vue-next';

defineProps({
    open: Boolean,
    title: { type: String, default: 'Emin misin?' },
    message: { type: String, default: 'Bu işlem geri alınamaz.' },
    confirmLabel: { type: String, default: 'Sil' },
    cancelLabel: { type: String, default: 'Vazgeç' },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center px-4" role="dialog" aria-modal="true" :aria-label="title">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="emit('cancel')" />

                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-950">
                            <AlertTriangle :size="20" class="text-red-600 dark:text-red-400" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">{{ title }}</h3>
                            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ message }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800"
                            @click="emit('cancel')"
                        >
                            {{ cancelLabel }}
                        </button>
                        <button
                            type="button"
                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700"
                            @click="emit('confirm')"
                        >
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
