<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import DarkModeToggle from './DarkModeToggle.vue';
import SearchModal from './SearchModal.vue';
import NavMenu from './NavMenu.vue';
import {
    Search, Menu, X, Home, LayoutDashboard,
    LogIn, UserPlus, LogOut, User, Bookmark
} from 'lucide-vue-next';

const page = usePage();
const drawerOpen = ref(false);
const searchOpen = ref(false);

const navMenu = computed(() => page.props.nav?.menu ?? []);
const user = computed(() => page.props.auth?.user ?? null);
const site = computed(() => page.props.site ?? {});
const isAdmin = computed(() => Boolean(user.value?.is_admin));
const isLoggedIn = computed(() => Boolean(user.value));

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

function closeDrawer() {
    drawerOpen.value = false;
}

function logout() {
    closeDrawer();
    router.post(route('logout'));
}

function initials(label = '') {
    return label.slice(0, 2).toUpperCase();
}
</script>

<template>
    <!-- Overlay -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="drawerOpen"
                class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
                @click="closeDrawer"
            />
        </Transition>

        <!-- Offcanvas Drawer -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-300 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <aside
                v-if="drawerOpen"
                class="fixed top-0 right-0 bottom-0 z-50 w-72 bg-white dark:bg-neutral-900 border-l border-neutral-200 dark:border-neutral-800 flex flex-col shadow-xl"
            >
                <!-- Drawer header -->
                <div class="flex items-center justify-between h-16 px-5 border-b border-neutral-200 dark:border-neutral-800 shrink-0">
                    <Link :href="route('home')" class="flex items-center gap-3 text-neutral-900 dark:text-white" @click="closeDrawer">
                        <img
                            v-if="site.logo_url"
                            :src="site.logo_url"
                            :alt="site.site_name"
                            class="h-9 w-9 rounded-xl object-cover ring-1 ring-neutral-200 dark:ring-neutral-800"
                        />
                        <span v-else class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-xs font-semibold text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-300">
                            {{ initials(site.site_name || 'FSK Blog') }}
                        </span>
                        <span class="font-bold tracking-tight">{{ site.site_name || 'FSK Blog' }}</span>
                    </Link>
                    <button
                        class="p-2 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                        @click="closeDrawer"
                    >
                        <X :size="18" />
                    </button>
                </div>

                <!-- Drawer body -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Navigation -->
                    <div class="p-4">
                        <p class="text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-2 px-2">Gezinme</p>
                        <nav class="space-y-0.5">
                            <Link
                                :href="route('home')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
                                @click="closeDrawer"
                            >
                                <Home :size="16" class="shrink-0 text-neutral-400" />
                                Anasayfa
                            </Link>
                            <template v-for="item in navMenu" :key="item.id">
                                <component
                                    v-if="item.url"
                                    :is="itemComponent(item)"
                                    v-bind="itemProps(item)"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
                                    @click="closeDrawer"
                                >
                                    {{ item.label }}
                                </component>

                                <div
                                    v-else
                                    class="px-3 py-2.5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                                >
                                    {{ item.label }}
                                </div>

                                <div v-if="item.children?.length" class="ml-4 space-y-0.5">
                                    <component
                                        v-for="child in item.children"
                                        :key="child.id"
                                        :is="itemComponent(child)"
                                        v-bind="itemProps(child)"
                                        class="flex items-center px-3 py-2 rounded-lg text-sm text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
                                        @click="closeDrawer"
                                    >
                                        {{ child.label }}
                                    </component>
                                </div>
                            </template>
                        </nav>
                    </div>

                    <!-- Divider -->
                    <div class="mx-4 border-t border-neutral-100 dark:border-neutral-800" />

                    <!-- Auth section -->
                    <div class="p-4">
                        <p class="text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-2 px-2">Hesap</p>

                        <!-- Giriş yapılmamış -->
                        <div v-if="!isLoggedIn" class="space-y-0.5">
                            <Link
                                :href="route('login')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
                                @click="closeDrawer"
                            >
                                <LogIn :size="16" class="shrink-0 text-neutral-400" />
                                Giriş Yap
                            </Link>
                            <Link
                                :href="route('register')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors"
                                @click="closeDrawer"
                            >
                                <UserPlus :size="16" class="shrink-0" />
                                Kayıt Ol
                            </Link>
                        </div>

                        <!-- Giriş yapılmış -->
                        <div v-else class="space-y-0.5">
                            <!-- Kullanıcı bilgisi -->
                            <div class="flex items-center gap-3 px-3 py-2.5 mb-1">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center shrink-0">
                                    <User :size="14" class="text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ user.name }}</div>
                                    <div class="text-xs text-neutral-400 truncate">{{ user.email }}</div>
                                </div>
                            </div>

                            <!-- Kaydedilenler -->
                            <Link
                                :href="route('bookmarks.index')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
                                @click="closeDrawer"
                            >
                                <Bookmark :size="16" class="shrink-0 text-neutral-400" />
                                Kaydedilenler
                            </Link>

                            <!-- Admin paneli -->
                            <Link
                                v-if="isAdmin"
                                :href="route('admin.dashboard')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors"
                                @click="closeDrawer"
                            >
                                <LayoutDashboard :size="16" class="shrink-0" />
                                Admin Paneli
                            </Link>

                            <!-- Dashboard (normal kullanıcı) -->
                            <Link
                                v-else
                                :href="route('dashboard')"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white transition-colors"
                                @click="closeDrawer"
                            >
                                <LayoutDashboard :size="16" class="shrink-0 text-neutral-400" />
                                Hesabım
                            </Link>

                            <!-- Çıkış -->
                            <button
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-600 dark:hover:text-red-400 w-full transition-colors group"
                                @click="logout"
                            >
                                <LogOut :size="16" class="shrink-0 group-hover:scale-110 transition-transform" />
                                Çıkış Yap
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Drawer footer -->
                <div class="shrink-0 px-5 py-3 border-t border-neutral-200 dark:border-neutral-800 flex items-center justify-between">
                    <span class="text-xs text-neutral-400 dark:text-neutral-500">© 2025 {{ site.site_name || 'FSK Blog' }}</span>
                    <DarkModeToggle />
                </div>
            </aside>
        </Transition>
    </Teleport>

    <!-- Header -->
    <header class="sticky top-0 z-30 bg-white/80 dark:bg-neutral-950/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-4">
            <!-- Logo -->
            <Link :href="route('home')" class="mr-2 flex items-center gap-3 shrink-0 text-neutral-900 dark:text-white">
                <img
                    v-if="site.logo_url"
                    :src="site.logo_url"
                    :alt="site.site_name"
                    class="h-10 w-10 rounded-xl object-cover ring-1 ring-neutral-200 dark:ring-neutral-800"
                />
                <span v-else class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-sm font-semibold text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-300">
                    {{ initials(site.site_name || 'FSK Blog') }}
                </span>
                <span class="text-xl font-bold tracking-tight">{{ site.site_name || 'FSK Blog' }}</span>
            </Link>

            <!-- Desktop nav -->
            <nav class="hidden md:flex items-center gap-1 flex-1 min-w-0">
                <Link
                    :href="route('home')"
                    class="px-3 py-1.5 text-sm rounded-md text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors shrink-0"
                >
                    Anasayfa
                </Link>
                <NavMenu :items="navMenu" />
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-1 ml-auto">
                <!-- Arama -->
                <button
                    @click="searchOpen = true"
                    class="p-2 rounded-lg text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                    aria-label="Ara"
                >
                    <Search :size="18" />
                </button>

                <!-- Dark mode (desktop only) -->
                <div class="hidden md:block">
                    <DarkModeToggle />
                </div>

                <!-- Desktop: auth links -->
                <div class="hidden md:flex items-center gap-1 ml-1">
                    <template v-if="!isLoggedIn">
                        <Link
                            :href="route('login')"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                        >
                            Giriş Yap
                        </Link>
                        <Link
                            :href="route('register')"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                        >
                            Kayıt Ol
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            v-if="isAdmin"
                            :href="route('admin.dashboard')"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                        >
                            Admin
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="p-2 rounded-lg text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                            title="Hesabım"
                        >
                            <User :size="18" />
                        </Link>
                    </template>
                </div>

                <!-- Hamburger -->
                <button
                    class="p-2 rounded-lg text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                    :class="drawerOpen ? 'bg-neutral-100 dark:bg-neutral-800' : ''"
                    @click="drawerOpen = true"
                    aria-label="Menü"
                >
                    <Menu :size="19" />
                </button>
            </div>
        </div>
    </header>

    <SearchModal :open="searchOpen" @close="searchOpen = false" />
</template>
