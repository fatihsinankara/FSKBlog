<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search, X } from 'lucide-vue-next';

const props = defineProps({ open: Boolean });
const emit = defineEmits(['close']);

const query = ref('');

watch(() => props.open, (val) => {
    if (val) {
        query.value = '';
        setTimeout(() => document.getElementById('search-input')?.focus(), 50);
    }
});

function submit() {
    if (!query.value.trim()) return;
    router.get(route('search'), { q: query.value }, { preserveState: false });
    emit('close');
}

function onKeydown(e) {
    if (e.key === 'Escape') emit('close');
}
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
            <div v-if="open" class="fixed inset-0 z-50 flex items-start justify-center pt-24 px-4" @keydown="onKeydown">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="emit('close')" />

                <!-- Modal -->
                <div class="relative w-full max-w-xl bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <form @submit.prevent="submit" class="flex items-center gap-3 px-4 py-4">
                        <Search :size="18" class="text-neutral-400 flex-shrink-0" />
                        <input
                            id="search-input"
                            v-model="query"
                            type="text"
                            placeholder="Yazılarda ara..."
                            class="flex-1 bg-transparent text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 text-sm outline-none"
                        />
                        <button type="button" @click="emit('close')" class="p-1 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                            <X :size="18" />
                        </button>
                    </form>
                    <div class="border-t border-neutral-100 dark:border-neutral-800 px-4 py-2 text-xs text-neutral-400">
                        Enter ile ara · Esc ile kapat
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
