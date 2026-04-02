<script setup>
import { ref, computed } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const search = ref('');

const filtered = computed(() =>
    props.tags.filter(
        t => t.name.toLowerCase().includes(search.value.toLowerCase())
            && !props.modelValue.includes(t.id)
    )
);

function toggle(id) {
    const current = [...props.modelValue];
    const idx = current.indexOf(id);
    if (idx === -1) current.push(id);
    else current.splice(idx, 1);
    emit('update:modelValue', current);
}

function remove(id) {
    emit('update:modelValue', props.modelValue.filter(i => i !== id));
}

function tagName(id) {
    return props.tags.find(t => t.id === id)?.name ?? '';
}
</script>

<template>
    <div>
        <!-- Selected tags -->
        <div v-if="modelValue.length" class="flex flex-wrap gap-1 mb-2">
            <span
                v-for="id in modelValue"
                :key="id"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300"
            >
                {{ tagName(id) }}
                <button type="button" @click="remove(id)" class="hover:text-indigo-900 dark:hover:text-white">
                    <X :size="10" />
                </button>
            </span>
        </div>

        <!-- Search input -->
        <input
            v-model="search"
            type="text"
            placeholder="Tag ara..."
            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-2"
        />

        <!-- Dropdown -->
        <div v-if="filtered.length" class="flex flex-wrap gap-1">
            <button
                v-for="tag in filtered"
                :key="tag.id"
                type="button"
                @click="toggle(tag.id)"
                class="px-2 py-0.5 rounded-full text-xs border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
            >
                + {{ tag.name }}
            </button>
        </div>
        <p v-else-if="search" class="text-xs text-neutral-400 mt-1">Sonuç bulunamadı.</p>
    </div>
</template>
