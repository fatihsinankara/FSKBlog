<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import ConfirmDialog from '@/Components/Shared/ConfirmDialog.vue';
import { router, Link } from '@inertiajs/vue3';
import { Check, Trash2, ExternalLink } from 'lucide-vue-next';
import { formatDate } from '@/composables/useFormatDate';
import { useConfirm } from '@/composables/useConfirm';

defineProps({
    pending: Object,
    approved: Object,
});

const { confirmState, confirm, handleConfirm, handleCancel } = useConfirm();

function approve(id) {
    router.patch(route('admin.comments.approve', id), {}, { preserveScroll: true });
}

function destroy(id) {
    confirm('Bu yorumu silmek istediğine emin misin?', () => {
        router.delete(route('admin.comments.destroy', id), { preserveScroll: true });
    });
}


</script>

<template>
    <AdminLayout>
        <Head title="Yorumlar" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yorumlar</h1>
        </div>

        <!-- Pending -->
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-orange-600 dark:text-orange-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-orange-500 rounded-full" />
                Onay Bekleyen ({{ pending.total }})
            </h2>
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 divide-y divide-neutral-100 dark:divide-neutral-800">
                <div v-for="comment in pending.data" :key="comment.id" class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-neutral-900 dark:text-white">
                                    {{ comment.user?.name || comment.guest_name }}
                                </span>
                                <span v-if="!comment.user_id" class="text-xs px-1.5 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-neutral-500">Misafir</span>
                                <span v-if="comment.parent_id" class="text-xs px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300">Yanıt</span>
                                <span class="text-xs text-neutral-400">{{ formatDate(comment.created_at) }}</span>
                            </div>
                            <p v-if="comment.parent" class="mb-2 text-xs text-neutral-400">
                                Yanıtlanan kişi: {{ comment.parent.user?.name || comment.parent.guest_name }}
                            </p>
                            <p class="text-sm text-neutral-700 dark:text-neutral-300">{{ comment.body }}</p>
                            <Link v-if="comment.post" :href="route('posts.show', comment.post.slug)" class="text-xs text-indigo-500 hover:underline mt-1 inline-flex items-center gap-1">
                                {{ comment.post.title }}
                                <ExternalLink :size="10" />
                            </Link>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button @click="approve(comment.id)" class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-950 hover:bg-green-100 dark:hover:bg-green-900 rounded-lg transition-colors">
                                <Check :size="12" /> Onayla
                            </button>
                            <button @click="destroy(comment.id)" class="p-1.5 text-neutral-400 hover:text-red-500 transition-colors">
                                <Trash2 :size="14" />
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="!pending.data.length" class="px-6 py-8 text-center text-sm text-neutral-400">
                    Onay bekleyen yorum yok.
                </div>
            </div>
            <Pagination :links="pending.links" />
        </div>

        <!-- Approved -->
        <div>
            <h2 class="text-sm font-semibold text-green-600 dark:text-green-400 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full" />
                Onaylı Yorumlar ({{ approved.total }})
            </h2>
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 divide-y divide-neutral-100 dark:divide-neutral-800">
                <div v-for="comment in approved.data" :key="comment.id" class="p-5 flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-medium text-neutral-900 dark:text-white">{{ comment.user?.name || comment.guest_name }}</span>
                        <span v-if="comment.parent_id" class="text-xs px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300">Yanıt</span>
                        <span class="text-xs text-neutral-400">{{ formatDate(comment.created_at) }}</span>
                    </div>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ comment.body }}</p>
                    </div>
                    <button @click="destroy(comment.id)" class="p-1.5 text-neutral-400 hover:text-red-500 transition-colors flex-shrink-0">
                        <Trash2 :size="14" />
                    </button>
                </div>
                <div v-if="!approved.data.length" class="px-6 py-8 text-center text-sm text-neutral-400">
                    Onaylı yorum yok.
                </div>
            </div>
            <Pagination :links="approved.links" />
        </div>
        <ConfirmDialog
            :open="confirmState.open"
            :message="confirmState.message"
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />
    </AdminLayout>
</template>
