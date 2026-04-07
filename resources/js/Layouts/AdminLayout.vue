<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/Shared/FlashMessage.vue';
import {
    BookOpen,
    ChevronDown,
    Clock,
    Database,
    ExternalLink,
    FileStack,
    FileText,
    FolderOpen,
    LayoutDashboard,
    LogOut,
    Menu,
    MessageSquare,
    Navigation,
    Settings,
    Tag,
    User,
    Users,
    X,
} from 'lucide-vue-next';

const sidebarOpen = ref(false);
const page = usePage();
const site = computed(() => page.props.site ?? {});

const dashboardItem = { label: 'Dashboard', icon: LayoutDashboard, route: 'admin.dashboard' };

const navGroups = [
    {
        key: 'content',
        label: 'İçerik',
        icon: FileText,
        items: [
            { label: 'Yazılar', icon: FileText, route: 'admin.posts.index' },
            { label: 'Koleksiyonlar', icon: BookOpen, route: 'admin.collections.index' },
            { label: 'Sayfalar', icon: FileStack, route: 'admin.pages.index' },
        ],
    },
    {
        key: 'structure',
        label: 'Yapı ve Navigasyon',
        icon: Navigation,
        items: [
            { label: 'Menü', icon: Navigation, route: 'admin.menus.index' },
            { label: 'Kategoriler', icon: FolderOpen, route: 'admin.categories.index' },
            { label: 'Taglar', icon: Tag, route: 'admin.tags.index' },
        ],
    },
    {
        key: 'management',
        label: 'Topluluk ve Yönetim',
        icon: Users,
        items: [
            { label: 'Kullanıcılar', icon: Users, route: 'admin.users.index' },
            { label: 'Yorumlar', icon: MessageSquare, route: 'admin.comments.index' },
        ],
    },
    {
        key: 'system',
        label: 'Sistem',
        icon: Settings,
        items: [
            { label: 'Genel Ayarlar', icon: Settings, route: 'admin.settings.edit' },
            { label: 'Cache', icon: Database, route: 'admin.cache.index' },
            { label: 'Cron', icon: Clock, route: 'admin.cron.index' },
        ],
    },
];

const flatNavItems = navGroups.flatMap((group) => group.items);

// Bottom nav'da gösterilecek 5 öğe (en çok kullanılanlar)
const bottomNavItems = [
    { label: 'Panel', icon: LayoutDashboard, route: 'admin.dashboard' },
    { label: 'Yazılar', icon: FileText, route: 'admin.posts.index' },
    { label: 'Koleksiyon', icon: BookOpen, route: 'admin.collections.index' },
    { label: 'Kategoriler', icon: FolderOpen, route: 'admin.categories.index' },
    { label: 'Daha fazla', icon: Menu, route: null },
];

function isActive(routeName) {
    if (!routeName) {
        return false;
    }

    return route().current(routeName) || route().current(routeName + '.*');
}

const openGroups = ref(
    Object.fromEntries(
        navGroups.map((group) => [
            group.key,
            group.items.some((item) => isActive(item.route)),
        ]),
    ),
);

function isGroupActive(group) {
    return group.items.some((item) => isActive(item.route));
}

function isGroupOpen(group) {
    return openGroups.value[group.key] ?? false;
}

function toggleGroup(groupKey) {
    openGroups.value[groupKey] = !openGroups.value[groupKey];
}

function closeSidebar() {
    sidebarOpen.value = false;
}

const currentPageLabel = computed(() => {
    if (isActive(dashboardItem.route)) {
        return dashboardItem.label;
    }

    const found = flatNavItems.find((item) => isActive(item.route));

    return found ? found.label : 'Admin';
});

const isMoreActive = computed(() => {
    const bottomRoutes = bottomNavItems.filter((item) => item.route).map((item) => item.route);
    const current = [dashboardItem, ...flatNavItems].find((item) => isActive(item.route));

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
            @click="closeSidebar"
        />

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-800 transform transition-transform duration-300 flex flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
                <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-5 border-b border-neutral-200 dark:border-neutral-800 shrink-0">
                <Link :href="route('home')" class="flex items-center gap-3 font-bold tracking-tight text-neutral-900 dark:text-white">
                    <img
                        v-if="site.logo_url"
                        :src="site.logo_url"
                        :alt="site.site_name"
                        class="h-9 w-9 rounded-xl object-cover ring-1 ring-neutral-200 dark:ring-neutral-800"
                    />
                    <span v-else class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-xs font-semibold text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-300">
                        {{ (site.site_name || 'FSK Blog').slice(0, 2).toUpperCase() }}
                    </span>
                    <span>{{ site.site_name || 'FSK Blog' }}</span>
                </Link>
                <button
                    class="lg:hidden p-2 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                    @click="closeSidebar"
                >
                    <X :size="18" />
                </button>
            </div>

            <!-- Nav -->
            <nav class="admin-sidebar-scroll flex-1 overflow-y-auto p-3 space-y-2">
                <Link
                    :href="route(dashboardItem.route)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group"
                    :class="isActive(dashboardItem.route)
                        ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400'
                        : 'text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-neutral-100'"
                    @click="closeSidebar"
                >
                    <component
                        :is="dashboardItem.icon"
                        :size="17"
                        class="shrink-0 transition-transform duration-150"
                        :class="isActive(dashboardItem.route) ? '' : 'group-hover:scale-110'"
                    />
                    <span>{{ dashboardItem.label }}</span>
                    <span v-if="isActive(dashboardItem.route)" class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-500 dark:bg-indigo-400" />
                </Link>

                <div
                    v-for="group in navGroups"
                    :key="group.key"
                    class="rounded-2xl border border-neutral-200/80 bg-neutral-50/70 p-1.5 dark:border-neutral-800 dark:bg-neutral-950/40"
                >
                    <button
                        type="button"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition-colors"
                        :class="isGroupActive(group)
                            ? 'text-neutral-900 dark:text-white'
                            : 'text-neutral-500 dark:text-neutral-400 hover:bg-white dark:hover:bg-neutral-900'"
                        @click="toggleGroup(group.key)"
                    >
                        <component
                            :is="group.icon"
                            :size="17"
                            class="shrink-0"
                            :class="isGroupActive(group) ? 'text-indigo-600 dark:text-indigo-400' : ''"
                        />
                        <div class="min-w-0 flex-1">
                            <div>{{ group.label }}</div>
                            <div class="text-[11px] font-normal text-neutral-400 dark:text-neutral-500">
                                {{ group.items.length }} bağlantı
                            </div>
                        </div>
                        <ChevronDown
                            :size="16"
                            class="shrink-0 text-neutral-400 transition-transform duration-200"
                            :class="isGroupOpen(group) ? 'rotate-180' : ''"
                        />
                    </button>

                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="max-h-0 opacity-0"
                        enter-to-class="max-h-96 opacity-100"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="max-h-96 opacity-100"
                        leave-to-class="max-h-0 opacity-0"
                    >
                        <div v-if="isGroupOpen(group)" class="overflow-hidden px-1 pb-1 pt-1">
                            <Link
                                v-for="item in group.items"
                                :key="item.route"
                                :href="route(item.route)"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition-all duration-150 group"
                                :class="isActive(item.route)
                                    ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100 dark:bg-neutral-900 dark:text-indigo-400 dark:ring-indigo-950/80'
                                    : 'text-neutral-500 dark:text-neutral-400 hover:bg-white hover:text-neutral-900 dark:hover:bg-neutral-900 dark:hover:text-neutral-100'"
                                @click="closeSidebar"
                            >
                                <component
                                    :is="item.icon"
                                    :size="16"
                                    class="shrink-0 transition-transform duration-150"
                                    :class="isActive(item.route) ? '' : 'group-hover:scale-110'"
                                />
                                <span>{{ item.label }}</span>
                                <span v-if="isActive(item.route)" class="ml-auto h-1.5 w-1.5 rounded-full bg-indigo-500 dark:bg-indigo-400" />
                            </Link>
                        </div>
                    </Transition>
                </div>
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

<style scoped>
.admin-sidebar-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.admin-sidebar-scroll::-webkit-scrollbar {
    display: none;
}
</style>
