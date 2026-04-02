<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/Shared/FlashMessage.vue';
import {
    LayoutDashboard, FileText, Tag, FolderOpen,
    MessageSquare, LogOut, Menu, X, ChevronRight
} from 'lucide-vue-next';

const sidebarOpen = ref(false);
const page = usePage();

const navItems = [
    { label: 'Dashboard', icon: LayoutDashboard, route: 'admin.dashboard' },
    { label: 'Yazılar', icon: FileText, route: 'admin.posts.index' },
    { label: 'Kategoriler', icon: FolderOpen, route: 'admin.categories.index' },
    { label: 'Taglar', icon: Tag, route: 'admin.tags.index' },
    { label: 'Yorumlar', icon: MessageSquare, route: 'admin.comments.index' },
];

function isActive(routeName) {
    return route().current(routeName) || route().current(routeName + '.*');
}
</script>

<template>
    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100">
        <!-- Mobile overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false" />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-800 transform transition-transform duration-200"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="flex items-center justify-between h-16 px-6 border-b border-neutral-200 dark:border-neutral-800">
                <Link :href="route('home')" class="text-xl font-bold tracking-tight">
                    FSK Blog
                </Link>
                <button class="lg:hidden p-1 text-neutral-500" @click="sidebarOpen = false">
                    <X :size="20" />
                </button>
            </div>

            <nav class="p-4 space-y-1">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="isActive(item.route)
                        ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400'
                        : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                >
                    <component :is="item.icon" :size="18" />
                    {{ item.label }}
                </Link>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-neutral-200 dark:border-neutral-800">
                <div class="text-xs text-neutral-500 mb-2 px-3">
                    {{ page.props.auth.user?.name }}
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 w-full transition-colors"
                >
                    <LogOut :size="18" />
                    Çıkış Yap
                </Link>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:ml-64 flex flex-col min-h-screen">
            <!-- Topbar -->
            <header class="sticky top-0 z-10 h-16 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 flex items-center px-6 gap-4">
                <button class="lg:hidden p-1 text-neutral-500" @click="sidebarOpen = true">
                    <Menu :size="20" />
                </button>
                <div class="flex items-center gap-1 text-sm text-neutral-500 dark:text-neutral-400 ml-auto">
                    <Link :href="route('home')" class="hover:text-neutral-900 dark:hover:text-white transition-colors">
                        Siteye git
                    </Link>
                    <ChevronRight :size="14" />
                </div>
            </header>

            <main class="flex-1 p-6">
                <FlashMessage />
                <slot />
            </main>
        </div>
    </div>
</template>
