<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Bell, Bookmark, BookOpenCheck, FolderHeart, Layers3, Settings } from 'lucide-vue-next';
import { formatDate } from '@/composables/useFormatDate';

defineProps({
    stats: Object,
    recent_bookmarks: Array,
    recent_notifications: Array,
});

const cards = [
    { key: 'saved_bookmarks', label: 'Kaydedilen', icon: Bookmark },
    { key: 'reading_bookmarks', label: 'Okunuyor', icon: BookOpenCheck },
    { key: 'completed_bookmarks', label: 'Tamamlanan', icon: BookOpenCheck },
    { key: 'following_categories', label: 'Takip edilen kategori', icon: FolderHeart },
    { key: 'following_collections', label: 'Takip edilen seri', icon: Layers3 },
    { key: 'unread_notifications', label: 'Okunmamış bildirim', icon: Bell },
];
</script>

<template>
    <Head title="Hesabım" />

    <AccountLayout title="Hesabım" description="Kaydettiğin içerikleri, takiplerini ve hesap hareketlerini tek yerden yönet.">
        <div class="space-y-6">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="card in cards"
                    :key="card.key"
                    class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900"
                >
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ card.label }}</p>
                        <component :is="card.icon" :size="18" class="text-indigo-500" />
                    </div>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ stats[card.key] }}</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Son Kaydedilenler</h2>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">En son eklediğin veya güncellediğin içerikler.</p>
                        </div>
                        <Link :href="route('bookmarks.index')" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-300">
                            Tümünü gör
                        </Link>
                    </div>

                    <div v-if="recent_bookmarks.length" class="space-y-4">
                        <div
                            v-for="bookmark in recent_bookmarks"
                            :key="bookmark.id"
                            class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-800"
                        >
                            <div class="flex items-center gap-2 text-xs text-neutral-400">
                                <span class="rounded-full bg-neutral-100 px-2 py-0.5 uppercase tracking-wide dark:bg-neutral-800">{{ bookmark.status }}</span>
                                <span>{{ formatDate(bookmark.created_at) }}</span>
                            </div>
                            <h3 class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">
                                <Link v-if="bookmark.post" :href="route('posts.show', bookmark.post.slug)" class="hover:text-indigo-600 dark:hover:text-indigo-300">
                                    {{ bookmark.post.title }}
                                </Link>
                                <span v-else>Silinmiş içerik</span>
                            </h3>
                            <p v-if="bookmark.post?.excerpt" class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400">{{ bookmark.post.excerpt }}</p>
                        </div>
                    </div>

                    <div v-else class="rounded-xl border border-dashed border-neutral-200 px-4 py-8 text-center text-sm text-neutral-400 dark:border-neutral-800">
                        Henüz kaydedilmiş içerik yok.
                    </div>
                </section>

                <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Son Bildirimler</h2>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">Hesabınla ilgili son gelişmeler burada görünür.</p>
                        </div>
                        <Link :href="route('settings.edit')" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-300">
                            <Settings :size="14" />
                            Ayarlar
                        </Link>
                    </div>

                    <div v-if="recent_notifications.length" class="space-y-3">
                        <div
                            v-for="notification in recent_notifications"
                            :key="notification.id"
                            class="rounded-xl border px-4 py-3"
                            :class="notification.is_read
                                ? 'border-neutral-200 dark:border-neutral-800'
                                : 'border-indigo-200 bg-indigo-50/60 dark:border-indigo-900 dark:bg-indigo-950/20'"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ notification.title }}</p>
                                <span class="text-xs text-neutral-400">{{ formatDate(notification.created_at) }}</span>
                            </div>
                            <p v-if="notification.excerpt" class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ notification.excerpt }}</p>
                        </div>
                    </div>

                    <div v-else class="rounded-xl border border-dashed border-neutral-200 px-4 py-8 text-center text-sm text-neutral-400 dark:border-neutral-800">
                        Şimdilik yeni bildirim yok.
                    </div>
                </section>
            </div>
        </div>
    </AccountLayout>
</template>
