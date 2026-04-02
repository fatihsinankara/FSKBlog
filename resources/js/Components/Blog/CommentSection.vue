<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageCircle, Send } from 'lucide-vue-next';
import Pagination from '@/Components/Shared/Pagination.vue';

const props = defineProps({
    post: Object,
});

const page = usePage();
const user = page.props.auth.user;
const isAdmin = computed(() => Boolean(user?.is_admin));
const comments = computed(() => props.post.comments?.data ?? []);
const totalComments = computed(() => props.post.comments?.total ?? comments.value.length);

const form = useForm({
    body:        '',
    guest_name:  '',
    guest_email: '',
    website:     '',
});

function submit() {
    form.post(route('comments.store', props.post.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('body', 'guest_name', 'guest_email', 'website'),
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', {
        year: 'numeric', month: 'long', day: 'numeric',
    });
}
</script>

<template>
    <section class="mt-16 pt-12 border-t border-neutral-200 dark:border-neutral-800">
        <h2 class="text-xl font-semibold mb-8 flex items-center gap-2">
            <MessageCircle :size="20" class="text-indigo-500" />
            Yorumlar
            <span v-if="totalComments" class="text-sm font-normal text-neutral-500">({{ totalComments }})</span>
        </h2>

        <!-- Comments list -->
        <div v-if="comments.length" class="space-y-6 mb-10">
            <div
                v-for="comment in comments"
                :key="comment.id"
                class="flex gap-4"
            >
                <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-sm flex-shrink-0">
                    {{ (comment.author_name || 'A')[0].toUpperCase() }}
                </div>
                <div class="flex-1">
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-sm font-medium text-neutral-900 dark:text-white">{{ comment.author_name }}</span>
                        <span class="text-xs text-neutral-400">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed">{{ comment.body }}</p>
                </div>
            </div>
        </div>

        <p v-else class="text-sm text-neutral-500 dark:text-neutral-400 mb-10">
            Henüz yorum yok. İlk yorumu sen yap!
        </p>

        <!-- Comment form -->
        <div class="bg-neutral-50 dark:bg-neutral-900 rounded-2xl p-6 border border-neutral-200 dark:border-neutral-800">
            <h3 class="text-sm font-semibold mb-4 text-neutral-900 dark:text-white">Yorum Yaz</h3>

            <form @submit.prevent="submit" class="space-y-4">
                <input
                    v-model="form.website"
                    type="text"
                    tabindex="-1"
                    autocomplete="off"
                    class="hidden"
                    aria-hidden="true"
                />

                <!-- Guest fields -->
                <template v-if="!user">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">İsim *</label>
                            <input
                                v-model="form.guest_name"
                                type="text"
                                placeholder="Adın Soyadın"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-400': form.errors.guest_name }"
                            />
                            <p v-if="form.errors.guest_name" class="text-xs text-red-500 mt-1">{{ form.errors.guest_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">E-posta *</label>
                            <input
                                v-model="form.guest_email"
                                type="email"
                                placeholder="ornek@mail.com"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-400': form.errors.guest_email }"
                            />
                            <p v-if="form.errors.guest_email" class="text-xs text-red-500 mt-1">{{ form.errors.guest_email }}</p>
                        </div>
                    </div>
                </template>

                <div v-else class="text-xs text-neutral-500 dark:text-neutral-400 pb-1">
                    <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ user.name }}</span> olarak yorum yazıyorsun.
                </div>

                <div>
                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Yorum *</label>
                    <textarea
                        v-model="form.body"
                        rows="4"
                        placeholder="Yorumunu yaz..."
                        class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                        :class="{ 'border-red-400': form.errors.body }"
                    />
                    <p v-if="form.errors.body" class="text-xs text-red-500 mt-1">{{ form.errors.body }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <p v-if="!isAdmin" class="text-xs text-neutral-400">
                        Yorumunuz admin onayından sonra yayınlanacak.
                    </p>
                    <div v-else />
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg transition-colors"
                    >
                        <Send :size="14" />
                        {{ form.processing ? 'Gönderiliyor...' : 'Gönder' }}
                    </button>
                </div>
            </form>
        </div>

        <Pagination v-if="post.comments?.links" :links="post.comments.links" />
    </section>
</template>
