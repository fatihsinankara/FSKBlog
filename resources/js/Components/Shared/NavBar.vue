<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DarkModeToggle from './DarkModeToggle.vue';
import SearchModal from './SearchModal.vue';
import { Search, Menu, X } from 'lucide-vue-next';

const page = usePage();
const mobileOpen = ref(false);
const searchOpen = ref(false);

const navCategories = computed(() => page.props.nav?.categories ?? []);
const isAdmin = computed(() => Boolean(page.props.auth.user?.is_admin));
</script>

<template>
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-950/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-6">
            <!-- Logo -->
            <Link :href="route('home')" class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white mr-2">
                FSK Blog
            </Link>

            <!-- Desktop nav -->
            <nav class="hidden md:flex items-center gap-1 flex-1">
                <Link :href="route('home')" class="px-3 py-1.5 text-sm rounded-md text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                    Anasayfa
                </Link>
                <Link :href="route('categories.index')" class="px-3 py-1.5 text-sm rounded-md text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                    Kategoriler
                </Link>
                <template v-for="cat in navCategories" :key="cat.id">
                    <Link
                        :href="route('categories.show', cat.slug)"
                        class="px-3 py-1.5 text-sm rounded-md transition-colors"
                        :style="{ color: cat.color }"
                    >
                        {{ cat.name }}
                    </Link>
                </template>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-1 ml-auto">
                <button
                    @click="searchOpen = true"
                    class="p-2 rounded-lg text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                    aria-label="Ara"
                >
                    <Search :size="18" />
                </button>
                <DarkModeToggle />

                <Link
                    v-if="isAdmin"
                    :href="route('admin.dashboard')"
                    class="hidden md:inline-flex ml-1 px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                >
                    Admin
                </Link>

                <!-- Mobile menu button -->
                <button class="md:hidden p-2 text-neutral-500" @click="mobileOpen = !mobileOpen">
                    <X v-if="mobileOpen" :size="18" />
                    <Menu v-else :size="18" />
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-if="mobileOpen" class="md:hidden border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 px-4 py-3 space-y-1">
            <Link :href="route('home')" class="block px-3 py-2 text-sm rounded-md text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Anasayfa</Link>
            <Link :href="route('categories.index')" class="block px-3 py-2 text-sm rounded-md text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">Kategoriler</Link>
            <template v-for="cat in navCategories" :key="cat.id">
                <Link :href="route('categories.show', cat.slug)" class="block px-3 py-2 text-sm rounded-md text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">{{ cat.name }}</Link>
            </template>
            <Link v-if="isAdmin" :href="route('admin.dashboard')" class="block px-3 py-2 text-sm rounded-md text-indigo-600 dark:text-indigo-400 hover:bg-neutral-100 dark:hover:bg-neutral-800">Admin</Link>
        </div>
    </header>

    <SearchModal :open="searchOpen" @close="searchOpen = false" />
</template>
