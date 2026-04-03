<script setup>
import { ref } from 'vue';
import { Link2, Check, Twitter } from 'lucide-vue-next';

const props = defineProps({
    title: { type: String, required: true },
    url:   { type: String, required: true },
});

const copied = ref(false);

function shareTwitter() {
    const text = encodeURIComponent(props.title);
    const url  = encodeURIComponent(props.url);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'noopener,noreferrer');
}

function copyLink() {
    navigator.clipboard.writeText(props.url).then(() => {
        copied.value = true;
        setTimeout(() => { copied.value = false; }, 2000);
    });
}
</script>

<template>
    <div class="flex items-center gap-2">
        <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mr-1">Paylaş</span>

        <button
            @click="shareTwitter"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
            aria-label="Twitter/X'te paylaş"
        >
            <Twitter :size="13" />
            <span>Twitter</span>
        </button>

        <button
            @click="copyLink"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors"
            :class="copied
                ? 'border-green-300 dark:border-green-700 text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-950/30'
                : 'border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white'"
            :aria-label="copied ? 'Kopyalandı' : 'Bağlantıyı kopyala'"
        >
            <Check v-if="copied" :size="13" />
            <Link2 v-else :size="13" />
            <span>{{ copied ? 'Kopyalandı!' : 'Bağlantı' }}</span>
        </button>
    </div>
</template>
