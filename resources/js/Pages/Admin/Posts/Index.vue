<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Eye, ExternalLink } from 'lucide-vue-next';

defineProps({
    posts: Object,
});

function destroy(id) {
    if (confirm('Bu yazıyı silmek istediğine emin misin?')) {
        router.delete(route('admin.posts.destroy', id), { preserveScroll: true });
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AdminLayout>
        <Head title="Yazılar" />

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yazılar</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ posts.total }} yazı</p>
            </div>
            <Link
                :href="route('admin.posts.create')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors"
            >
                <Plus :size="16" />
                Yeni Yazı
            </Link>
        </div>

        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Başlık</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider hidden md:table-cell">Kategori</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider hidden lg:table-cell">Tarih</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Durum</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider hidden sm:table-cell">Görüntülenme</th>
                            <th class="px-6 py-3" />
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-neutral-50 dark:hover:bg-neutral-950 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-neutral-900 dark:text-white line-clamp-1">{{ post.title }}</p>
                                <div v-if="post.tags?.length" class="flex gap-1 mt-1">
                                    <span v-for="tag in post.tags.slice(0,3)" :key="tag.id" class="text-xs text-neutral-400">#{{ tag.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span v-if="post.category" class="text-xs font-medium" :style="{ color: post.category.color }">
                                    {{ post.category.name }}
                                </span>
                                <span v-else class="text-xs text-neutral-400">—</span>
                            </td>
                            <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400 hidden lg:table-cell text-xs">
                                {{ formatDate(post.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="post.status === 'published'
                                        ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                        : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300'"
                                >
                                    {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-neutral-500 dark:text-neutral-400 text-xs hidden sm:table-cell">
                                <span class="flex items-center justify-end gap-1">
                                    <Eye :size="12" />
                                    {{ post.views.toLocaleString() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        v-if="post.status === 'published'"
                                        :href="route('posts.show', post.slug)"
                                        target="_blank"
                                        class="p-1 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors"
                                    >
                                        <ExternalLink :size="14" />
                                    </Link>
                                    <Link
                                        :href="route('admin.posts.edit', post.id)"
                                        class="p-1 text-neutral-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                    >
                                        <Pencil :size="14" />
                                    </Link>
                                    <button
                                        @click="destroy(post.id)"
                                        class="p-1 text-neutral-400 hover:text-red-500 transition-colors"
                                    >
                                        <Trash2 :size="14" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :links="posts.links" />
    </AdminLayout>
</template>
