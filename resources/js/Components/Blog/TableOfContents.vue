<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { List } from 'lucide-vue-next';

const props = defineProps({
    contentSelector: { type: String, default: '.blog-prose' },
});

const headings = ref([]);
const activeId  = ref('');

function buildHeadings() {
    const container = document.querySelector(props.contentSelector);
    if (!container) return;

    const els = container.querySelectorAll('h2, h3');
    headings.value = Array.from(els).map((el, i) => {
        const slug = el.textContent
            .toLowerCase()
            .replace(/[^a-z0-9\s\u00c0-\u024f]/g, '')
            .trim()
            .replace(/\s+/g, '-');
        const id = `heading-${slug}-${i}`;
        el.id = id;
        return {
            id,
            text: el.textContent,
            level: parseInt(el.tagName[1]),
        };
    });
}

function onScroll() {
    if (!headings.value.length) return;

    const offset = 100;
    let current = headings.value[0]?.id ?? '';

    for (const h of headings.value) {
        const el = document.getElementById(h.id);
        if (el && el.getBoundingClientRect().top <= offset) {
            current = h.id;
        }
    }
    activeId.value = current;
}

function scrollTo(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const top = el.getBoundingClientRect().top + window.scrollY - 80;
    window.scrollTo({ top, behavior: 'smooth' });
}

onMounted(() => {
    buildHeadings();
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
    <nav v-if="headings.length > 1" class="text-sm">
        <div class="flex items-center gap-2 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-3">
            <List :size="13" />
            İçindekiler
        </div>
        <ul class="space-y-0.5 border-l border-neutral-200 dark:border-neutral-800">
            <li v-for="h in headings" :key="h.id">
                <button
                    @click="scrollTo(h.id)"
                    class="w-full text-left py-1 transition-colors leading-snug"
                    :class="[
                        h.level === 3 ? 'pl-6 text-xs' : 'pl-3 text-sm',
                        activeId === h.id
                            ? 'text-indigo-600 dark:text-indigo-400 border-l-2 border-indigo-500 -ml-px font-medium'
                            : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white'
                    ]"
                >
                    {{ h.text }}
                </button>
            </li>
        </ul>
    </nav>
</template>
