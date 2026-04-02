<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatsCard from '@/Components/Admin/StatsCard.vue';
import { Link } from '@inertiajs/vue3';
import { FileText, Eye, Send, FileEdit, Pencil } from 'lucide-vue-next';

defineProps({
    stats: Object,
    recent_posts: Array,
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
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Blogunun genel durumu</p>
        </div>

        <!-- Stats grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <StatsCard label="Toplam Yazı" :value="stats.total_posts" :icon="FileText" color="indigo" />
            <StatsCard label="Yayınlanan" :value="stats.published_posts" :icon="Send" color="green" />
            <StatsCard label="Taslak" :value="stats.draft_posts" :icon="FileEdit" color="yellow" />
            <StatsCard label="Toplam Görüntülenme" :value="stats.total_views.toLocaleString()" :icon="Eye" color="blue" />
        </div>

        <!-- Recent posts -->
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
                <h2 class="font-semibold text-neutral-900 dark:text-white">Son Yazılar</h2>
                <Link :href="route('admin.posts.create')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                    + Yeni Yazı
                </Link>
            </div>
            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <div v-for="post in recent_posts" :key="post.id" class="flex items-center justify-between px-6 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ post.title }}</p>
                        <p class="text-xs text-neutral-400 mt-0.5">{{ formatDate(post.created_at) }}</p>
                    </div>
                    <div class="flex items-center gap-3 ml-4">
                        <span
                            class="text-xs px-2 py-0.5 rounded-full font-medium"
                            :class="post.status === 'published'
                                ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300'"
                        >
                            {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
                        </span>
                        <Link :href="route('admin.posts.edit', post.id)" class="p-1 text-neutral-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <Pencil :size="14" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
