<script setup>
import { ref, computed } from 'vue';
import { Columns2, FileText } from 'lucide-vue-next';

const props = defineProps({
    modelValue: String,
    error: String,
});
const emit = defineEmits(['update:modelValue']);

const mode = ref('split');

const rendered = computed(() => {
    if (!props.modelValue) return '<p class="text-neutral-400 text-sm italic">Önizleme burada görünecek...</p>';

    const html = props.modelValue
        .replace(/^#{6}\s(.+)/gm, '<h6>$1</h6>')
        .replace(/^#{5}\s(.+)/gm, '<h5>$1</h5>')
        .replace(/^#{4}\s(.+)/gm, '<h4>$1</h4>')
        .replace(/^#{3}\s(.+)/gm, '<h3>$1</h3>')
        .replace(/^#{2}\s(.+)/gm, '<h2>$1</h2>')
        .replace(/^#{1}\s(.+)/gm, '<h1>$1</h1>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/`(.+?)`/g, '<code>$1</code>')
        .replace(/^- (.+)/gm, '<li>$1</li>')
        .replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>')
        .replace(/\n\n/g, '</p><p>')
        .replace(/^(?!<[h1-6ul])/gm, '');

    return html;
});
</script>

<template>
    <div>
        <!-- Toolbar -->
        <div class="flex items-center gap-2 mb-2">
            <button
                type="button"
                @click="mode = 'split'"
                class="flex items-center gap-1 px-2 py-1 text-xs rounded transition-colors"
                :class="mode === 'split' ? 'bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-white' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
            >
                <Columns2 :size="14" /> Split
            </button>
            <button
                type="button"
                @click="mode = 'write'"
                class="flex items-center gap-1 px-2 py-1 text-xs rounded transition-colors"
                :class="mode === 'write' ? 'bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-white' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300'"
            >
                <FileText :size="14" /> Yaz
            </button>
        </div>

        <div
            class="border rounded-xl overflow-hidden"
            :class="error ? 'border-red-400' : 'border-neutral-200 dark:border-neutral-700'"
            :style="{ display: 'grid', gridTemplateColumns: mode === 'split' ? '1fr 1fr' : '1fr' }"
        >
            <!-- Editor -->
            <textarea
                :value="modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                rows="20"
                placeholder="# Başlık&#10;&#10;Yazını burada **Markdown** formatında yaz..."
                class="w-full p-4 text-sm font-mono bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 resize-none outline-none"
                :class="mode === 'split' ? 'border-r border-neutral-200 dark:border-neutral-700' : ''"
            />

            <!-- Preview -->
            <div
                v-if="mode === 'split'"
                class="p-4 overflow-auto bg-neutral-50 dark:bg-neutral-950 prose prose-sm prose-neutral dark:prose-invert max-w-none"
                v-html="rendered"
            />
        </div>

        <p v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</p>
    </div>
</template>
