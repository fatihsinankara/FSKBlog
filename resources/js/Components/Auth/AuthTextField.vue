<script setup>
import { computed, onMounted, ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    autocomplete: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    revealable: {
        type: Boolean,
        default: false,
    },
});

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);
const revealed = ref(false);
const inputType = computed(() => (props.revealable && revealed.value ? 'text' : props.type));

onMounted(() => {
    if (props.autofocus) {
        input.value?.focus();
    }
});

function toggleReveal() {
    revealed.value = !revealed.value;
}

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <div>
        <div class="mb-1.5 flex items-center justify-between gap-3">
            <label :for="id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                {{ label }}
            </label>
            <slot name="labelAction" />
        </div>

        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400 dark:text-neutral-500">
                <slot name="icon" />
            </div>

            <input
                :id="id"
                ref="input"
                v-model="model"
                :type="inputType"
                :autocomplete="autocomplete"
                :required="required"
                :placeholder="placeholder"
                class="h-11 w-full rounded-xl border border-neutral-200 bg-neutral-50 py-2.5 pl-10 pr-3 text-sm text-neutral-900 placeholder-neutral-400 outline-none transition-colors focus:border-transparent focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                :class="[
                    error ? 'border-red-400 focus:ring-red-400' : '',
                    revealable ? 'pr-11' : ''
                ]"
            />

            <button
                v-if="revealable"
                type="button"
                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center rounded-r-xl text-neutral-400 transition-colors hover:text-neutral-700 dark:text-neutral-500 dark:hover:text-neutral-200"
                :aria-label="revealed ? 'Şifreyi gizle' : 'Şifreyi göster'"
                @click="toggleReveal"
            >
                <EyeOff v-if="revealed" :size="17" />
                <Eye v-else :size="17" />
            </button>
        </div>

        <p v-if="error" class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ error }}</p>
    </div>
</template>
