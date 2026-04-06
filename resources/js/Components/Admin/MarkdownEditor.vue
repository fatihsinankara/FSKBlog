<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import {
    Bold, Italic, Heading2, Heading3, Link2, Image, Code, CodeSquare,
    List, ListOrdered, Quote, Minus, Eye, Pencil, Columns2,
    Strikethrough, Table, Upload, Loader2,
} from 'lucide-vue-next';

const props = defineProps({
    modelValue: String,
    error: String,
    uploadUrl: {
        type: String,
        default: '/admin/upload/image',
    },
});
const emit = defineEmits(['update:modelValue']);

const textarea = ref(null);
const fileInput = ref(null);
const mode = ref('write');
const isLgScreen = ref(false);
const uploading = ref(false);
const uploadError = ref('');

// --- Responsive: detect lg+ screens ---
function checkScreen() {
    isLgScreen.value = window.innerWidth >= 1024;
    if (!isLgScreen.value && mode.value === 'split') {
        mode.value = 'write';
    }
}

onMounted(() => {
    checkScreen();
    window.addEventListener('resize', checkScreen);
});
onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreen);
});

// --- Preview renderer (basic markdown to HTML) ---
function escapeHtml(value) {
    return value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

const rendered = computed(() => {
    if (!props.modelValue) return '<p class="text-neutral-400 text-sm italic">Önizleme burada görünecek...</p>';

    const html = escapeHtml(props.modelValue)
        .replace(/^######\s(.+)/gm, '<h6>$1</h6>')
        .replace(/^#####\s(.+)/gm, '<h5>$1</h5>')
        .replace(/^####\s(.+)/gm, '<h4>$1</h4>')
        .replace(/^###\s(.+)/gm, '<h3>$1</h3>')
        .replace(/^##\s(.+)/gm, '<h2>$1</h2>')
        .replace(/^#\s(.+)/gm, '<h1>$1</h1>')
        .replace(/^&gt;\s(.+)/gm, '<blockquote><p>$1</p></blockquote>')
        .replace(/```(\w*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>')
        .replace(/~~(.+?)~~/g, '<del>$1</del>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/`(.+?)`/g, '<code>$1</code>')
        .replace(/!\[(.*?)\]\((.*?)\)/g, '<img src="$2" alt="$1" class="rounded-lg max-w-full" />')
        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-indigo-600 underline">$1</a>')
        .replace(/^---$/gm, '<hr />')
        .replace(/^\d+\.\s(.+)/gm, '<li>$1</li>')
        .replace(/^- (.+)/gm, '<li>$1</li>')
        .replace(/\n\n/g, '</p><p>')
        .replace(/\n/g, '<br>');

    return `<p>${html}</p>`;
});

// --- Toolbar actions ---
function getSelection() {
    const el = textarea.value;
    return {
        start: el.selectionStart,
        end: el.selectionEnd,
        text: props.modelValue?.substring(el.selectionStart, el.selectionEnd) || '',
    };
}

function replaceSelection(before, after, placeholder = '') {
    const el = textarea.value;
    const sel = getSelection();
    const content = sel.text || placeholder;
    const val = props.modelValue || '';

    const newValue = val.substring(0, sel.start) + before + content + after + val.substring(sel.end);
    emit('update:modelValue', newValue);

    nextTick(() => {
        el.focus();
        const cursorStart = sel.start + before.length;
        const cursorEnd = cursorStart + content.length;
        el.setSelectionRange(cursorStart, cursorEnd);
    });
}

function wrapLine(prefix) {
    const el = textarea.value;
    const val = props.modelValue || '';
    const start = el.selectionStart;

    const lineStart = val.lastIndexOf('\n', start - 1) + 1;
    const lineEnd = val.indexOf('\n', start);
    const end = lineEnd === -1 ? val.length : lineEnd;
    const line = val.substring(lineStart, end);

    if (line.startsWith(prefix)) {
        const newValue = val.substring(0, lineStart) + line.substring(prefix.length) + val.substring(end);
        emit('update:modelValue', newValue);
        nextTick(() => {
            el.focus();
            el.setSelectionRange(start - prefix.length, start - prefix.length);
        });
    } else {
        const newValue = val.substring(0, lineStart) + prefix + line + val.substring(end);
        emit('update:modelValue', newValue);
        nextTick(() => {
            el.focus();
            el.setSelectionRange(start + prefix.length, start + prefix.length);
        });
    }
}

function insertAtCursor(text) {
    const el = textarea.value;
    const val = props.modelValue || '';
    const start = el?.selectionStart ?? val.length;
    const newValue = val.substring(0, start) + text + val.substring(el?.selectionEnd ?? val.length);
    emit('update:modelValue', newValue);

    nextTick(() => {
        if (el) {
            el.focus();
            const pos = start + text.length;
            el.setSelectionRange(pos, pos);
        }
    });
}

// --- Image Upload ---
async function uploadImage(file) {
    if (!file || !file.type.startsWith('image/')) return;

    const maxSize = 4 * 1024 * 1024; // 4MB
    if (file.size > maxSize) {
        uploadError.value = "Dosya boyutu 4MB'dan büyük olamaz.";
        setTimeout(() => uploadError.value = '', 4000);
        return;
    }

    uploading.value = true;
    uploadError.value = '';

    const formData = new FormData();
    formData.append('image', file);

    try {
        const { data } = await window.axios.post(props.uploadUrl, formData);
        const altText = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
        insertAtCursor(`\n![${altText}](${data.url})\n`);
    } catch (err) {
        const msg = err.response?.data?.errors?.image?.[0]
            || err.response?.data?.message
            || 'Görsel yüklenirken hata oluştu.';
        uploadError.value = msg;
        setTimeout(() => uploadError.value = '', 4000);
    } finally {
        uploading.value = false;
    }
}

function onFileSelect(e) {
    const file = e.target.files?.[0];
    if (file) uploadImage(file);
    e.target.value = '';
}

function triggerFileInput() {
    fileInput.value?.click();
}

// --- Drag & Drop ---
const isDragging = ref(false);

function onDragOver(e) {
    e.preventDefault();
    isDragging.value = true;
}

function onDragLeave() {
    isDragging.value = false;
}

function onDrop(e) {
    e.preventDefault();
    isDragging.value = false;
    const file = e.dataTransfer?.files?.[0];
    if (file && file.type.startsWith('image/')) {
        uploadImage(file);
    }
}

// --- Paste from clipboard ---
function onPaste(e) {
    const items = e.clipboardData?.items;
    if (!items) return;

    for (const item of items) {
        if (item.type.startsWith('image/')) {
            e.preventDefault();
            const file = item.getAsFile();
            if (file) uploadImage(file);
            return;
        }
    }
}

const actions = [
    { icon: Bold, label: 'Kalın', shortcut: 'Ctrl+B', action: () => replaceSelection('**', '**', 'kalın metin') },
    { icon: Italic, label: 'İtalik', shortcut: 'Ctrl+I', action: () => replaceSelection('*', '*', 'italik metin') },
    { icon: Strikethrough, label: 'Üstü çizili', action: () => replaceSelection('~~', '~~', 'üstü çizili') },
    { divider: true },
    { icon: Heading2, label: 'Başlık 2', action: () => wrapLine('## ') },
    { icon: Heading3, label: 'Başlık 3', action: () => wrapLine('### ') },
    { divider: true },
    { icon: List, label: 'Liste', action: () => wrapLine('- ') },
    { icon: ListOrdered, label: 'Sıralı liste', action: () => wrapLine('1. ') },
    { icon: Quote, label: 'Alıntı', action: () => wrapLine('> ') },
    { divider: true },
    { icon: Code, label: 'Kod', shortcut: 'Ctrl+E', action: () => replaceSelection('`', '`', 'kod') },
    { icon: CodeSquare, label: 'Kod bloğu', action: () => replaceSelection('```\n', '\n```', 'kod bloğu') },
    { divider: true },
    { icon: Link2, label: 'Link', shortcut: 'Ctrl+K', action: () => replaceSelection('[', '](url)', 'link metni') },
    { icon: Image, label: 'Görsel', action: () => replaceSelection('![', '](url)', 'alt text') },
    { icon: Upload, label: 'Görsel yükle', action: triggerFileInput },
    { icon: Table, label: 'Tablo', action: () => replaceSelection('\n| Başlık | Başlık |\n|--------|--------|\n| ', ' | hücre |\n', 'hücre') },
    { icon: Minus, label: 'Ayırıcı çizgi', action: () => replaceSelection('\n---\n', '', '') },
];

// --- Keyboard shortcuts ---
function onKeydown(e) {
    if ((e.ctrlKey || e.metaKey) && !e.shiftKey) {
        const key = e.key.toLowerCase();
        if (key === 'b') {
            e.preventDefault();
            replaceSelection('**', '**', 'kalın metin');
        } else if (key === 'i') {
            e.preventDefault();
            replaceSelection('*', '*', 'italik metin');
        } else if (key === 'e') {
            e.preventDefault();
            replaceSelection('`', '`', 'kod');
        } else if (key === 'k') {
            e.preventDefault();
            replaceSelection('[', '](url)', 'link metni');
        }
    }

    if (e.key === 'Tab') {
        e.preventDefault();
        const el = textarea.value;
        const start = el.selectionStart;
        const val = props.modelValue || '';
        const newValue = val.substring(0, start) + '    ' + val.substring(el.selectionEnd);
        emit('update:modelValue', newValue);
        nextTick(() => {
            el.focus();
            el.setSelectionRange(start + 4, start + 4);
        });
    }
}
</script>

<template>
    <div>
        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            accept="image/jpeg,image/png,image/webp,image/gif"
            class="hidden"
            @change="onFileSelect"
        />

        <!-- Toolbar -->
        <div class="rounded-t-xl border border-b-0 bg-neutral-50 dark:bg-neutral-950"
             :class="error ? 'border-red-400' : 'border-neutral-200 dark:border-neutral-700'">

            <!-- Top row: formatting buttons -->
            <div class="flex items-center gap-0.5 px-2 py-1.5 overflow-x-auto scrollbar-none">
                <template v-for="(item, i) in actions" :key="i">
                    <div v-if="item.divider" class="mx-1 h-4 w-px bg-neutral-200 dark:bg-neutral-700 shrink-0 hidden sm:block" />
                    <button
                        v-else
                        type="button"
                        @click="item.action"
                        :title="item.label + (item.shortcut ? ` (${item.shortcut})` : '')"
                        :aria-label="item.label"
                        class="flex items-center justify-center w-8 h-8 rounded-lg text-neutral-500 hover:text-neutral-900 hover:bg-neutral-200/70 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-800 transition-colors shrink-0"
                        :class="{ 'text-indigo-500 dark:text-indigo-400': item.icon === Upload }"
                    >
                        <component :is="item.icon" :size="15" />
                    </button>
                </template>

                <!-- Spacer -->
                <div class="flex-1" />

                <!-- Upload indicator -->
                <div v-if="uploading" class="flex items-center gap-1.5 text-xs text-indigo-500 shrink-0 mr-2">
                    <Loader2 :size="14" class="animate-spin" />
                    <span class="hidden sm:inline">Yükleniyor...</span>
                </div>

                <!-- Mode switches -->
                <div class="flex items-center gap-0.5 rounded-lg bg-neutral-200/60 dark:bg-neutral-800 p-0.5 shrink-0">
                    <button
                        type="button"
                        @click="mode = 'write'"
                        :title="'Yazma modu'"
                        class="flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-colors"
                        :class="mode === 'write'
                            ? 'bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white shadow-sm'
                            : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                    >
                        <Pencil :size="12" />
                        <span class="hidden sm:inline">Yaz</span>
                    </button>
                    <button
                        v-if="isLgScreen"
                        type="button"
                        @click="mode = 'split'"
                        :title="'Bölünmüş görünüm'"
                        class="flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-colors"
                        :class="mode === 'split'
                            ? 'bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white shadow-sm'
                            : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                    >
                        <Columns2 :size="12" />
                        <span class="hidden sm:inline">Split</span>
                    </button>
                    <button
                        type="button"
                        @click="mode = 'preview'"
                        :title="'Önizleme'"
                        class="flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-colors"
                        :class="mode === 'preview'
                            ? 'bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white shadow-sm'
                            : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                    >
                        <Eye :size="12" />
                        <span class="hidden sm:inline">Önizle</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Editor body -->
        <div
            class="border rounded-b-xl overflow-hidden relative"
            :class="error ? 'border-red-400' : 'border-neutral-200 dark:border-neutral-700'"
            @dragover="onDragOver"
            @dragleave="onDragLeave"
            @drop="onDrop"
        >
            <!-- Drag overlay -->
            <div
                v-if="isDragging"
                class="absolute inset-0 z-10 flex items-center justify-center bg-indigo-50/90 dark:bg-indigo-950/90 border-2 border-dashed border-indigo-400 dark:border-indigo-500 rounded-b-xl"
            >
                <div class="text-center">
                    <Upload :size="32" class="mx-auto mb-2 text-indigo-500" />
                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Görseli buraya bırakın</p>
                </div>
            </div>

            <div
                class="grid"
                :class="{
                    'grid-cols-1': mode !== 'split',
                    'grid-cols-2': mode === 'split',
                }"
            >
                <!-- Textarea -->
                <textarea
                    v-show="mode !== 'preview'"
                    ref="textarea"
                    :value="modelValue"
                    @input="emit('update:modelValue', $event.target.value)"
                    @keydown="onKeydown"
                    @paste="onPaste"
                    rows="22"
                    placeholder="# Başlık&#10;&#10;Yazını burada **Markdown** formatında yaz...&#10;&#10;Görsel yüklemek için sürükle-bırak yapın veya panodan yapıştırın."
                    class="w-full p-4 text-sm leading-relaxed font-mono bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white placeholder-neutral-400 dark:placeholder-neutral-500 resize-y outline-none min-h-[400px]"
                    :class="mode === 'split' ? 'border-r border-neutral-200 dark:border-neutral-700' : ''"
                />

                <!-- Preview -->
                <div
                    v-show="mode === 'split' || mode === 'preview'"
                    class="p-4 overflow-auto bg-neutral-50/50 dark:bg-neutral-950/50 prose prose-sm prose-neutral dark:prose-invert max-w-none min-h-[400px]"
                    :class="{ 'max-h-[600px]': mode === 'split' }"
                    v-html="rendered"
                />
            </div>
        </div>

        <!-- Footer hints -->
        <div class="mt-1.5 flex items-center justify-between">
            <p v-if="uploadError" class="text-xs text-red-500">{{ uploadError }}</p>
            <p v-else-if="error" class="text-xs text-red-500">{{ error }}</p>
            <p v-else class="text-[10px] text-neutral-400 dark:text-neutral-500">
                Markdown desteklenir. Görsel yüklemek için sürükle-bırak, panodan yapıştır veya
                <button type="button" @click="triggerFileInput" class="underline hover:text-neutral-600 dark:hover:text-neutral-300">dosya seçin</button>.
                <span class="hidden sm:inline">
                    <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[10px]">Ctrl+B</kbd> kalın,
                    <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[10px]">Ctrl+I</kbd> italik,
                    <kbd class="px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-[10px]">Ctrl+K</kbd> link
                </span>
            </p>
        </div>
    </div>
</template>
