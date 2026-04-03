<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatsCard from '@/Components/Admin/StatsCard.vue';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Eye, FileEdit, FileText, Pencil, Send } from 'lucide-vue-next';

defineProps({
    stats: Object,
    recent_posts: Array,
    top_posts_by_views: Array,
    top_posts_by_bookmarks: Array,
    top_posts_by_comments: Array,
    category_performance: Array,
    collection_performance: Array,
    daily_metrics: Array,
    newsletter_growth: Array,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">İçerik üretimi, etkileşim ve büyüme metriklerinin özeti.</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-10">
            <StatsCard label="Toplam Yazı" :value="stats.total_posts" :icon="FileText" color="indigo" />
            <StatsCard label="Yayınlanan" :value="stats.published_posts" :icon="Send" color="green" />
            <StatsCard label="Taslak" :value="stats.draft_posts" :icon="FileEdit" color="yellow" />
            <StatsCard label="Toplam Görüntülenme" :value="stats.total_views.toLocaleString()" :icon="Eye" color="blue" />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-10">
            <StatsCard label="Kaydedilenler" :value="stats.total_bookmarks.toLocaleString()" :icon="BookOpen" color="indigo" />
            <StatsCard label="Onay Bekleyen" :value="stats.pending_comments.toLocaleString()" :icon="Pencil" color="yellow" />
            <StatsCard label="Bülten Abonesi" :value="stats.subscribers.toLocaleString()" :icon="Send" color="green" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h2 class="font-semibold text-neutral-900 dark:text-white">Son Yazılar</h2>
                    <Link :href="route('admin.posts.create')" class="text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                        + Yeni Yazı
                    </Link>
                </div>
                <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <div v-for="post in recent_posts" :key="post.id" class="flex items-center justify-between gap-4 px-6 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-neutral-900 dark:text-white">{{ post.title }}</p>
                            <p class="mt-0.5 text-xs text-neutral-400">{{ formatDate(post.created_at) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="post.status === 'published'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'"
                            >
                                {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
                            </span>
                            <Link :href="route('admin.posts.edit', post.id)" class="p-1 text-neutral-400 transition-colors hover:text-indigo-600 dark:hover:text-indigo-400">
                                <Pencil :size="14" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">Son 14 Gün Özeti</h2>
                <div class="space-y-3">
                    <div v-for="item in daily_metrics" :key="item.date" class="rounded-xl bg-neutral-50 px-4 py-3 dark:bg-neutral-800/60">
                        <div class="flex flex-col gap-2 text-sm md:flex-row md:items-center md:justify-between">
                            <span class="font-medium text-neutral-700 dark:text-neutral-200">{{ formatDate(item.date) }}</span>
                            <div class="flex flex-wrap gap-4 text-neutral-500 dark:text-neutral-400">
                                <span>{{ item.views }} görüntülenme</span>
                                <span>{{ item.new_bookmarks }} kayıt</span>
                                <span>{{ item.new_comments }} yorum</span>
                            </div>
                        </div>
                    </div>
                    <p v-if="!daily_metrics.length" class="text-sm text-neutral-400">Henüz günlük metric verisi oluşmadı.</p>
                </div>
            </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">En Çok Okunanlar</h2>
                <div class="space-y-3">
                    <div v-for="post in top_posts_by_views" :key="post.id" class="flex items-center justify-between gap-4">
                        <p class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ post.title }}</p>
                        <span class="text-xs text-neutral-400">{{ post.views }} görüntülenme</span>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">En Çok Kaydedilenler</h2>
                <div class="space-y-3">
                    <div v-for="post in top_posts_by_bookmarks" :key="post.id" class="flex items-center justify-between gap-4">
                        <p class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ post.title }}</p>
                        <span class="text-xs text-neutral-400">{{ post.bookmarks_count }} kayıt</span>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">En Çok Yorum Alanlar</h2>
                <div class="space-y-3">
                    <div v-for="post in top_posts_by_comments" :key="post.id" class="flex items-center justify-between gap-4">
                        <p class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ post.title }}</p>
                        <span class="text-xs text-neutral-400">{{ post.approved_comments_count }} yorum</span>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">Bülten Büyümesi</h2>
                <div class="space-y-3">
                    <div v-for="item in newsletter_growth" :key="item.date" class="flex items-center justify-between gap-4">
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ formatDate(item.date) }}</p>
                        <span class="text-xs text-neutral-400">{{ item.total }} yeni abone</span>
                    </div>
                    <p v-if="!newsletter_growth.length" class="text-sm text-neutral-400">Henüz abonelik verisi oluşmadı.</p>
                </div>
            </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">Kategori Performansı</h2>
                <div class="space-y-3">
                    <div v-for="category in category_performance" :key="category.id" class="flex items-center justify-between gap-4">
                        <p class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ category.name }}</p>
                        <span class="text-xs text-neutral-400">{{ category.published_posts_count }} yazı · {{ category.total_views || 0 }} görüntülenme</span>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 font-semibold text-neutral-900 dark:text-white">Seri Performansı</h2>
                <div class="space-y-3">
                    <div v-for="collection in collection_performance" :key="collection.id" class="flex items-center justify-between gap-4">
                        <p class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ collection.title }}</p>
                        <span class="text-xs text-neutral-400">{{ collection.posts_count }} bölüm · {{ collection.total_views || 0 }} görüntülenme</span>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
