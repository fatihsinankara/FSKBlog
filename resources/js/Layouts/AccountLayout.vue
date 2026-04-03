<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { LayoutDashboard, Bookmark, Settings, Shield } from 'lucide-vue-next';

const props = defineProps({
    title: String,
    description: String,
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const navItems = computed(() => {
    const items = [
        { label: 'Hesabım', route: 'dashboard', icon: LayoutDashboard },
        { label: 'Kaydedilenler', route: 'bookmarks.index', icon: Bookmark },
        { label: 'Ayarlar', route: 'settings.edit', icon: Settings },
    ];

    if (user.value?.is_admin) {
        items.push({ label: 'Admin Paneli', route: 'admin.dashboard', icon: Shield });
    }

    return items;
});

function isActive(routeName) {
    return route().current(routeName) || route().current(`${routeName}.*`);
}
</script>

<template>
    <AppLayout>
        <section class="border-b border-neutral-200 bg-neutral-50/80 dark:border-neutral-800 dark:bg-neutral-900/40">
            <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-500">Hesap</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ title }}</h1>
                <p v-if="description" class="mt-2 max-w-2xl text-sm leading-7 text-neutral-500 dark:text-neutral-400">{{ description }}</p>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <div class="grid gap-6 lg:grid-cols-[17rem_minmax(0,1fr)]">
                <aside class="h-fit rounded-2xl border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                    <nav class="space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors"
                            :class="isActive(item.route)
                                ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-300'
                                : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white'"
                        >
                            <component :is="item.icon" :size="16" class="shrink-0" />
                            {{ item.label }}
                        </Link>
                    </nav>
                </aside>

                <div class="min-w-0">
                    <slot />
                </div>
            </div>
        </section>
    </AppLayout>
</template>
