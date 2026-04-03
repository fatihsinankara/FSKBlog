<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/Shared/FlashMessage.vue';
import {
    LayoutDashboard, FileText, Tag, FolderOpen,
    MessageSquare, Database, BookOpen, LogOut, Menu, X, ExternalLink, User,
    FileStack, Navigation
} from 'lucide-vue-next';

const sidebarOpen = ref(false);
const page = usePage();

const navItems = [
    { label: 'Dashboard', icon: LayoutDashboard, route: 'admin.dashboard' },
    { label: 'Yazılar', icon: FileText, route: 'admin.posts.index' },
    { label: 'Koleksiyonlar', icon: BookOpen, route: 'admin.collections.index' },
    { label: 'Sayfalar', icon: FileStack, route: 'admin.pages.index' },
    { label: 'Menü', icon: Navigation, route: 'admin.menus.index' },
    { label: 'Kategoriler', icon: FolderOpen, route: 'admin.categories.index' },
    { label: 'Taglar', icon: Tag, route: 'admin.tags.index' },
    { label: 'Yorumlar', icon: MessageSquare, route: 'admin.comments.index' },
    { label: 'Cache', icon: Database, route: 'admin.cache.index' },
];

// Bottom nav'da gösterilecek 5 öğe (en çok kullanılanlar)
const bottomNavItems = [
    { label: 'Panel', icon: LayoutDashboard, route: 'admin.dashboard' },
    { label: 'Yazılar', icon: FileText, route: 'admin.posts.index' },
    { label: 'Koleksiyon', icon: BookOpen, route: 'admin.collections.index' },
    { label: 'Kategoriler', icon: FolderOpen, route: 'admin.categories.index' },
    { label: 'Daha fazla', icon: Menu, route: null },
];

function isActive(routeName) {
    if (!routeName) return false;
    return route().current(routeName) || route().current(routeName + '.*');
}

const currentPageLabel = computed(() => {
    const found = navItems.find(item => isActive(item.route));
    return found ? found.label : 'Admin';
});

const isMoreActive = computed(() => {
    const bottomRoutes = bottomNavItems.filter(i => i.route).map(i => i.route);
    const current = navItems.find(item => isActive(item.route));
    if (!current) return false;
    return !bottomRoutes.includes(current.route);
});
</script>

<template>
    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100">
        <!-- Mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-800 transform transition-transform duration-300 flex flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-5 border-b border-neutral-200 dark:border-neutral-800 shrink-0">
                <Link :href="route('home')" class="flex items-center gap-2 font-bold tracking-tight text-neutral-900 dark:text-white">
                    <span class="text-indigo-600 dark:text-indigo-400 text-xl">FSK</span>
                    <span class="text-neutral-400 dark:text-neutral-600 font-light">Blog</span>
                </Link>
                <button
                    class="lg:hidden p-2 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                    @click="sidebarOpen = false"
                >
                    <X :size="18" />
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-0.5">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group"
                    :class="isActive(item.route)
                        ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400'
                        : 'text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-neutral-100'"
                    @click="sidebarOpen = false"
                >
                    <component
                        :is="item.icon"
                        :size="17"
                        class="shrink-0 transition-transform duration-150"
                        :class="isActive(item.route) ? '' : 'group-hover:scale-110'"
                    />
                    <span>{{ item.label }}</span>
                    <!-- Active indicator -->
                    <span v-if="isActive(item.route)" class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-500 dark:bg-indigo-400" />
                </Link>
            </nav>

            <!-- User section -->
            <div class="shrink-0 p-3 border-t border-neutral-200 dark:border-neutral-800">
                <div class="flex items-center gap-3 px-3 py-2 mb-1">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center shrink-0">
                        <User :size="14" class="text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate">
                            {{ page.props.auth.user?.name }}
                        </div>
                        <div class="text-xs text-neutral-400 dark:text-neutral-500">Admin</div>
                    </div>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-neutral-500 dark:text-neutral-400 hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-600 dark:hover:text-red-400 w-full transition-all duration-150 group"
                >
                    <LogOut :size="16" class="shrink-0 group-hover:scale-110 transition-transform duration-150" />
                    Çıkış Yap
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:ml-64 flex flex-col min-h-screen pb-16 lg:pb-0">
            <!-- Topbar -->
            <header class="sticky top-0 z-10 h-14 bg-white/90 dark:bg-neutral-900/90 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-800 flex items-center px-4 gap-3">
                <!-- Mobile menu button -->
                <button
                    class="lg:hidden p-2 -ml-1 rounded-lg text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                    @click="sidebarOpen = true"
                >
                    <Menu :size="19" />
                </button>

                <!-- Page title (mobile) -->
                <span class="lg:hidden text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                    {{ currentPageLabel }}
                </span>

                <!-- Desktop: empty left side -->
                <div class="hidden lg:flex items-center gap-1.5">
                    <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Admin</span>
                    <span class="text-neutral-200 dark:text-neutral-700">/</span>
                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ currentPageLabel }}</span>
                </div>

                <!-- Right side -->
                <div class="ml-auto flex items-center gap-2">
                    <Link
                        :href="route('home')"
                        class="flex items-center gap-1.5 text-xs font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors px-2.5 py-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800"
                    >
                        <ExternalLink :size="13" />
                        <span class="hidden sm:inline">Siteye git</span>
                    </Link>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6">
                <FlashMessage />
                <slot />
            </main>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="lg:hidden fixed bottom-0 inset-x-0 z-20 bg-white/95 dark:bg-neutral-900/95 backdrop-blur-md border-t border-neutral-200 dark:border-neutral-800 flex">
            <template v-for="item in bottomNavItems" :key="item.label">
                <!-- "Daha fazla" butonu -->
                <button
                    v-if="!item.route"
                    class="flex-1 flex flex-col items-center justify-center gap-0.5 py-2 text-xs font-medium transition-colors"
                    :class="isMoreActive
                        ? 'text-indigo-600 dark:text-indigo-400'
                        : 'text-neutral-400 dark:text-neutral-500'"
                    @click="sidebarOpen = true"
                >
                    <Menu :size="20" />
                    <span>Daha fazla</span>
                </button>

                <!-- Normal nav item -->
                <Link
                    v-else
                    :href="route(item.route)"
                    class="flex-1 flex flex-col items-center justify-center gap-0.5 py-2 text-xs font-medium transition-colors"
                    :class="isActive(item.route)
                        ? 'text-indigo-600 dark:text-indigo-400'
                        : 'text-neutral-400 dark:text-neutral-500'"
                >
                    <div class="relative">
                        <component :is="item.icon" :size="20" />
                        <!-- Active dot -->
                        <span
                            v-if="isActive(item.route)"
                            class="absolute -top-0.5 -right-0.5 w-1.5 h-1.5 rounded-full bg-indigo-500"
                        />
                    </div>
                    <span>{{ item.label }}</span>
                </Link>
            </template>
        </nav>
    </div>
</template>
