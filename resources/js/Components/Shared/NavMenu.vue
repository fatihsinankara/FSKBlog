<script setup>
/**
 * NavMenu — Priority+ responsive navigation.
 *
 * Shows as many top-level items as fit in the container.
 * Overflow items collapse into a "Daha fazla ▼" dropdown.
 * Each top-level item may have 1 level of children rendered as a submenu.
 *
 * Props
 *   items  — array of { id, label, url, open_in_new_tab, children[] }
 */
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import CategoryIcon from './CategoryIcon.vue';

const props = defineProps({
    items: { type: Array, default: () => [] },
});

const page = usePage();
const categories = computed(() => page.props.nav?.categories ?? []);
const categoryBySlug = computed(() => Object.fromEntries(
    categories.value.map((category) => [category.slug, category])
));

// ── Measurement ─────────────────────────────────────────────────────────────
const containerRef  = ref(null);
const measureRef    = ref(null);
const containerWidth = ref(0);
const itemWidths     = ref([]);  // px width of each top-level item slot
const moreWidth      = ref(96);  // px width of "Daha fazla" button

const topItems = computed(() => props.items);

// How many top-level items fit in the container
const visibleCount = computed(() => {
    if (!containerWidth.value || !itemWidths.value.length) return topItems.value.length;

    let used = 0;
    const total = itemWidths.value.length;

    for (let i = 0; i < total; i++) {
        const isLast = i === total - 1;
        const willNeedMore = !isLast; // if there's a next item we'd need "more"
        const spaceNeeded = used + itemWidths.value[i] + (willNeedMore ? moreWidth.value : 0);

        if (spaceNeeded <= containerWidth.value) {
            used += itemWidths.value[i];
        } else {
            // i items fit (0..i-1) but we need the "more" button in their space
            // Check if i items + more button fit
            return i;
        }
    }

    return total; // all fit
});

const visibleItems  = computed(() => topItems.value.slice(0, visibleCount.value));
const overflowItems = computed(() => topItems.value.slice(visibleCount.value));
const hasOverflow   = computed(() => overflowItems.value.length > 0);

function isExternalUrl(url = '') {
    return /^(https?:)?\/\//.test(url) || url.startsWith('mailto:') || url.startsWith('tel:');
}

function itemComponent(item) {
    return isExternalUrl(item.url ?? '') ? 'a' : Link;
}

function itemProps(item) {
    const props = {
        href: item.url,
    };

    if (item.open_in_new_tab) {
        props.target = '_blank';
        props.rel = 'noreferrer noopener';
    }

    return props;
}

async function measureItems() {
    await nextTick();
    if (!measureRef.value) return;

    const els = measureRef.value.querySelectorAll('[data-measure-item]');
    itemWidths.value = Array.from(els).map(el => el.offsetWidth + 4); // +4 gap

    const moreEl = measureRef.value.querySelector('[data-measure-more]');
    if (moreEl) moreWidth.value = moreEl.offsetWidth + 4;
}

let ro;
onMounted(async () => {
    await measureItems();
    ro = new ResizeObserver(entries => {
        containerWidth.value = entries[0].contentRect.width;
    });
    if (containerRef.value) {
        ro.observe(containerRef.value);
        containerWidth.value = containerRef.value.offsetWidth;
    }
});
onBeforeUnmount(() => ro?.disconnect());

watch(() => props.items, measureItems, { deep: true });

// ── Dropdown state ───────────────────────────────────────────────────────────
const openId      = ref(null); // id of open submenu (top-level item)
const moreOpen    = ref(false);

function toggleItem(id) {
    openId.value  = openId.value === id ? null : id;
    moreOpen.value = false;
}
function toggleMore() {
    moreOpen.value = !moreOpen.value;
    openId.value   = null;
}
function close() {
    openId.value   = null;
    moreOpen.value = false;
}

function hasOwnLink(item) {
    return Boolean(item.url);
}

function categoryForItem(item) {
    if (item?.type !== 'category') {
        return null;
    }

    return categoryBySlug.value[item.target] ?? null;
}

function categoryIconName(item) {
    return categoryForItem(item)?.icon || 'folder';
}

function categoryIconStyle(item) {
    return {
        color: categoryForItem(item)?.color || undefined,
    };
}

// Close on outside click
function onDocClick(e) {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        close();
    }
}
onMounted(() => document.addEventListener('click', onDocClick, true));
onBeforeUnmount(() => document.removeEventListener('click', onDocClick, true));
</script>

<template>
    <!-- ── Hidden measurement layer ── -->
    <div
        ref="measureRef"
        class="absolute invisible pointer-events-none flex items-center gap-1"
        aria-hidden="true"
        style="top:-9999px;left:0"
    >
        <div
            v-for="item in topItems"
            :key="item.id"
            data-measure-item
            class="px-3 py-1.5 text-sm rounded-md whitespace-nowrap flex items-center gap-1 shrink-0"
        >
            <CategoryIcon
                v-if="item.type === 'category'"
                :name="categoryIconName(item)"
                :size="14"
                :style="categoryIconStyle(item)"
            />
            {{ item.label }}
            <ChevronDown v-if="item.children?.length" :size="12" />
        </div>
        <div
            data-measure-more
            class="px-3 py-1.5 text-sm rounded-md whitespace-nowrap flex items-center gap-1 shrink-0"
        >
            Daha fazla <ChevronDown :size="12" />
        </div>
    </div>

    <!-- ── Actual nav ── -->
    <div ref="containerRef" class="flex items-center gap-1 flex-1 min-w-0">

        <!-- Visible items -->
        <template v-for="item in visibleItems" :key="item.id">

            <!-- With children → submenu dropdown -->
            <div v-if="item.children?.length" class="relative shrink-0">
                <button
                    @click.stop="toggleItem(item.id)"
                    class="px-3 py-1.5 text-sm rounded-md flex items-center gap-1 whitespace-nowrap transition-colors"
                    :class="openId === item.id
                        ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white'
                        : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                >
                    <CategoryIcon
                        v-if="item.type === 'category'"
                        :name="categoryIconName(item)"
                        :size="14"
                        class="shrink-0"
                        :style="categoryIconStyle(item)"
                    />
                    {{ item.label }}
                    <ChevronDown
                        :size="12"
                        class="transition-transform duration-150"
                        :class="{ 'rotate-180': openId === item.id }"
                    />
                </button>
                <Transition
                    enter-active-class="transition-all duration-150 ease-out"
                    enter-from-class="opacity-0 -translate-y-1 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition-all duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 -translate-y-1 scale-95"
                >
                    <div
                        v-if="openId === item.id"
                        class="absolute top-full left-0 mt-1.5 min-w-44 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-lg py-1 z-50"
                    >
                        <component
                            :is="itemComponent(item)"
                            v-if="hasOwnLink(item)"
                            v-bind="itemProps(item)"
                            @click="close"
                            class="block px-4 py-2 text-sm font-medium text-neutral-900 dark:text-white hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors border-b border-neutral-100 dark:border-neutral-800"
                        >
                            <CategoryIcon
                                v-if="item.type === 'category'"
                                :name="categoryIconName(item)"
                                :size="15"
                                class="mr-2 inline-block align-[-0.18em]"
                                :style="categoryIconStyle(item)"
                            />
                            {{ item.label }}
                        </component>
                        <component
                            v-for="child in item.children"
                            :key="child.id"
                            :is="itemComponent(child)"
                            v-bind="itemProps(child)"
                            @click="close"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                        >
                            <CategoryIcon
                                v-if="child.type === 'category'"
                                :name="categoryIconName(child)"
                                :size="15"
                                class="shrink-0"
                                :style="categoryIconStyle(child)"
                            />
                            {{ child.label }}
                        </component>
                    </div>
                </Transition>
            </div>

            <!-- No children → direct link -->
            <component
                v-else
                :is="itemComponent(item)"
                v-bind="itemProps(item)"
                class="px-3 py-1.5 text-sm rounded-md whitespace-nowrap shrink-0 transition-colors text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800"
            >
                <CategoryIcon
                    v-if="item.type === 'category'"
                    :name="categoryIconName(item)"
                    :size="14"
                    class="mr-1 inline-block align-[-0.18em]"
                    :style="categoryIconStyle(item)"
                />
                {{ item.label }}
            </component>
        </template>

        <!-- "Daha fazla" overflow button -->
        <div v-if="hasOverflow" class="relative shrink-0">
            <button
                @click.stop="toggleMore"
                class="px-3 py-1.5 text-sm rounded-md flex items-center gap-1 whitespace-nowrap transition-colors"
                :class="moreOpen
                    ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white'
                    : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800'"
            >
                Daha fazla
                <ChevronDown
                    :size="12"
                    class="transition-transform duration-150"
                    :class="{ 'rotate-180': moreOpen }"
                />
            </button>
            <Transition
                enter-active-class="transition-all duration-150 ease-out"
                enter-from-class="opacity-0 -translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition-all duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 -translate-y-1 scale-95"
            >
                <div
                    v-if="moreOpen"
                    class="absolute top-full left-0 mt-1.5 min-w-44 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-lg py-1 z-50"
                >
                    <template v-for="item in overflowItems" :key="item.id">
                        <!-- Item with children: label header + indented children -->
                        <template v-if="item.children?.length">
                            <div class="px-4 pt-2 pb-1 text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">
                                {{ item.label }}
                            </div>
                            <component
                                :is="itemComponent(item)"
                                v-if="hasOwnLink(item)"
                                v-bind="itemProps(item)"
                                @click="close"
                                class="block px-4 py-2 text-sm font-medium text-neutral-900 dark:text-white hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                            >
                                <CategoryIcon
                                    v-if="item.type === 'category'"
                                    :name="categoryIconName(item)"
                                    :size="15"
                                    class="mr-2 inline-block align-[-0.18em]"
                                    :style="categoryIconStyle(item)"
                                />
                                {{ item.label }}
                            </component>
                            <component
                                v-for="child in item.children"
                                :key="child.id"
                                :is="itemComponent(child)"
                                v-bind="itemProps(child)"
                                @click="close"
                                class="flex items-center gap-2 py-2 pl-6 pr-4 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                            >
                                <CategoryIcon
                                    v-if="child.type === 'category'"
                                    :name="categoryIconName(child)"
                                    :size="15"
                                    class="shrink-0"
                                    :style="categoryIconStyle(child)"
                                />
                                {{ child.label }}
                            </component>
                        </template>
                        <!-- Direct link -->
                        <component
                            v-else
                            :is="itemComponent(item)"
                            v-bind="itemProps(item)"
                            @click="close"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                        >
                            <CategoryIcon
                                v-if="item.type === 'category'"
                                :name="categoryIconName(item)"
                                :size="15"
                                class="shrink-0"
                                :style="categoryIconStyle(item)"
                            />
                            {{ item.label }}
                        </component>
                    </template>
                </div>
            </Transition>
        </div>
    </div>
</template>
