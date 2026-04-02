<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { X, CheckCircle, AlertCircle } from 'lucide-vue-next';

const page = usePage();
const visible = ref(false);
const message = computed(() => page.props.flash?.message);
const error = computed(() => page.props.flash?.error);

watch([message, error], ([msg, err]) => {
    if (msg || err) {
        visible.value = true;
        setTimeout(() => (visible.value = false), 4000);
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible && (message || error)"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium"
            :class="error
                ? 'bg-red-50 dark:bg-red-950 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300'
                : 'bg-green-50 dark:bg-green-950 border-green-200 dark:border-green-800 text-green-700 dark:text-green-300'"
        >
            <AlertCircle v-if="error" :size="16" />
            <CheckCircle v-else :size="16" />
            {{ error || message }}
            <button @click="visible = false" class="ml-2 opacity-60 hover:opacity-100">
                <X :size="14" />
            </button>
        </div>
    </Transition>
</template>
