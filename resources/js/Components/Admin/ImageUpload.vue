<script setup>
import { ref } from 'vue';
import { Upload, X } from 'lucide-vue-next';

const props = defineProps({
    currentUrl: String,
    error: String,
    accept: {
        type: String,
        default: 'image/*',
    },
    helpText: {
        type: String,
        default: 'JPG, PNG, WebP - max 4MB',
    },
});
const emit = defineEmits(['change']);

const preview = ref(props.currentUrl || null);
const dragging = ref(false);

function onFile(file) {
    if (!file || !file.type.startsWith('image/')) return;
    preview.value = URL.createObjectURL(file);
    emit('change', file);
}

function onInput(e) {
    onFile(e.target.files[0]);
}

function onDrop(e) {
    dragging.value = false;
    onFile(e.dataTransfer.files[0]);
}

function remove() {
    preview.value = null;
    emit('change', null);
}
</script>

<template>
    <div>
        <div
            v-if="!preview"
            class="relative border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors"
            :class="[
                dragging ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-950' : 'border-neutral-300 dark:border-neutral-700 hover:border-neutral-400 dark:hover:border-neutral-600',
                error ? 'border-red-400' : ''
            ]"
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="onDrop"
            @click="$refs.input.click()"
        >
            <input ref="input" type="file" :accept="accept" class="hidden" @change="onInput" />
            <Upload :size="24" class="mx-auto mb-2 text-neutral-400" />
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Görseli sürükle bırak veya <span class="text-indigo-600 dark:text-indigo-400">tıkla</span>
            </p>
            <p class="text-xs text-neutral-400 mt-1">{{ helpText }}</p>
        </div>

        <div v-else class="relative rounded-xl overflow-hidden border border-neutral-200 dark:border-neutral-700 aspect-video">
            <img :src="preview" alt="Yüklenen görsel önizlemesi" class="w-full h-full object-cover" />
            <button
                type="button"
                @click="remove"
                class="absolute top-2 right-2 w-7 h-7 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-colors"
            >
                <X :size="14" />
            </button>
        </div>

        <p v-if="error" class="text-xs text-red-500 mt-1">{{ error }}</p>
    </div>
</template>
