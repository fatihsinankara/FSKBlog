<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageCircle, Send, Heart, CornerDownRight } from 'lucide-vue-next';
import Pagination from '@/Components/Shared/Pagination.vue';
import TurnstileWidget from '@/Components/Shared/TurnstileWidget.vue';
import { formatDateLong as formatDate } from '@/composables/useFormatDate';

const props = defineProps({
    post: Object,
});

const page = usePage();
const user = page.props.auth.user;
const isAdmin = computed(() => Boolean(user?.is_admin));
const comments = computed(() => props.post.comments?.data ?? []);
const totalComments = computed(() => props.post.comments?.total ?? comments.value.length);
const replyingTo = ref(null);
const turnstile = ref(null);

const form = useForm({
    body:        '',
    guest_name:  '',
    guest_email: '',
    website:     '',
    parent_id:   null,
    cf_turnstile_response: '',
});

function submit() {
    form.post(route('comments.store', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('body', 'guest_name', 'guest_email', 'website', 'parent_id', 'cf_turnstile_response');
            replyingTo.value = null;
        },
        onFinish: () => turnstile.value?.reset(),
    });
}

// --- Yorum beğeni (optimistic, fetch tabanlı) ---
// liked: { [commentId]: boolean }, counts: { [commentId]: number }
const liked  = ref({});
const counts = ref({});

function getLiked(comment)  { return liked.value[comment.id]  ?? false; }
function getCount(comment)  { return counts.value[comment.id] ?? (comment.likes_count ?? 0); }

async function toggleLike(comment) {
    if (!user) return;

    const wasLiked = getLiked(comment);
    // Optimistik güncelleme
    liked.value[comment.id]  = !wasLiked;
    counts.value[comment.id] = getCount(comment) + (wasLiked ? -1 : 1);

    try {
        const res = await fetch(route('comments.like', comment.id), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Accept': 'application/json',
            },
        });
        if (res.ok) {
            const data = await res.json();
            liked.value[comment.id]  = data.liked;
            counts.value[comment.id] = data.count;
        } else {
            // Hata: geri al
            liked.value[comment.id]  = wasLiked;
            counts.value[comment.id] = getCount(comment) + (wasLiked ? 1 : -1);
        }
    } catch {
        liked.value[comment.id]  = wasLiked;
        counts.value[comment.id] = getCount(comment) + (wasLiked ? 1 : -1);
    }
}

function startReply(comment) {
    replyingTo.value = comment;
    form.parent_id = comment.id;
}

function cancelReply() {
    replyingTo.value = null;
    form.parent_id = null;
}
</script>

<template>
    <section class="mt-16 pt-12 border-t border-neutral-200 dark:border-neutral-800">
        <h2 class="text-xl font-semibold mb-8 flex items-center gap-2">
            <MessageCircle :size="20" class="text-indigo-500" />
            Yorumlar
            <span v-if="totalComments" class="text-sm font-normal text-neutral-500">({{ totalComments }})</span>
        </h2>

        <!-- Yorumlar listesi -->
        <div v-if="comments.length" class="space-y-6 mb-10">
            <div
                v-for="comment in comments"
                :key="comment.id"
                class="flex gap-4 group/comment"
            >
                <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-sm flex-shrink-0">
                    {{ (comment.author_name || 'A')[0].toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-sm font-medium text-neutral-900 dark:text-white">{{ comment.author_name }}</span>
                        <span class="text-xs text-neutral-400">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed">{{ comment.body }}</p>

                    <!-- Beğeni butonu -->
                    <div class="mt-2 flex items-center gap-1">
                        <button
                            @click="toggleLike(comment)"
                            :disabled="!user"
                            class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs transition-colors"
                            :class="[
                                !user ? 'cursor-default' : 'cursor-pointer',
                                getLiked(comment)
                                    ? 'text-rose-500 dark:text-rose-400'
                                    : 'text-neutral-400 dark:text-neutral-500 hover:text-rose-500 dark:hover:text-rose-400'
                            ]"
                            :title="!user ? 'Beğenmek için giriş yapın' : (getLiked(comment) ? 'Beğeniyi geri al' : 'Beğen')"
                            :aria-label="getLiked(comment) ? 'Beğeniyi geri al' : 'Yorumu beğen'"
                        >
                            <Heart
                                :size="13"
                                :fill="getLiked(comment) ? 'currentColor' : 'none'"
                            />
                            <span v-if="getCount(comment) > 0">{{ getCount(comment) }}</span>
                        </button>
                        <button
                            type="button"
                            class="flex items-center gap-1 rounded-lg px-2 py-1 text-xs text-neutral-400 transition-colors hover:text-indigo-500 dark:text-neutral-500 dark:hover:text-indigo-300"
                            @click="startReply(comment)"
                            :aria-label="'Yanıtla: ' + comment.author_name"
                        >
                            <CornerDownRight :size="13" />
                            Yanıtla
                        </button>
                    </div>

                    <div v-if="comment.replies?.length" class="mt-4 space-y-4 border-l border-neutral-200 pl-4 dark:border-neutral-800">
                        <div v-for="reply in comment.replies" :key="reply.id" class="flex gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                                {{ (reply.author_name || 'A')[0].toUpperCase() }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="mb-1 flex items-baseline gap-2">
                                    <span class="text-sm font-medium text-neutral-900 dark:text-white">{{ reply.author_name }}</span>
                                    <span class="text-xs text-neutral-400">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <p class="text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">{{ reply.body }}</p>
                                <div class="mt-2 flex items-center gap-1">
                                    <button
                                        @click="toggleLike(reply)"
                                        :disabled="!user"
                                        class="flex items-center gap-1 px-2 py-1 rounded-lg text-xs transition-colors"
                                        :class="[
                                            !user ? 'cursor-default' : 'cursor-pointer',
                                            getLiked(reply)
                                                ? 'text-rose-500 dark:text-rose-400'
                                                : 'text-neutral-400 dark:text-neutral-500 hover:text-rose-500 dark:hover:text-rose-400'
                                        ]"
                                        :aria-label="getLiked(reply) ? 'Beğeniyi geri al' : 'Yanıtı beğen'"
                                    >
                                        <Heart :size="13" :fill="getLiked(reply) ? 'currentColor' : 'none'" />
                                        <span v-if="getCount(reply) > 0">{{ getCount(reply) }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p v-else class="text-sm text-neutral-500 dark:text-neutral-400 mb-10">
            Henüz yorum yok. İlk yorumu sen yap!
        </p>

        <!-- Yorum formu -->
        <div class="bg-neutral-50 dark:bg-neutral-900 rounded-2xl p-6 border border-neutral-200 dark:border-neutral-800">
            <h3 class="text-sm font-semibold mb-4 text-neutral-900 dark:text-white">Yorum Yaz</h3>

            <div v-if="replyingTo" class="mb-4 flex items-start justify-between gap-3 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:border-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-300">
                <div>
                    <strong>{{ replyingTo.author_name }}</strong> yorumuna yanıt yazıyorsun.
                </div>
                <button type="button" class="text-xs font-medium" @click="cancelReply">
                    Vazgeç
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <input
                    v-model="form.website"
                    type="text"
                    tabindex="-1"
                    autocomplete="off"
                    class="hidden"
                    aria-hidden="true"
                />
                <input v-model="form.parent_id" type="hidden" />

                <!-- Misafir alanları -->
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
                                class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

                <TurnstileWidget
                    v-if="!user"
                    ref="turnstile"
                    v-model="form.cf_turnstile_response"
                    :error="form.errors.cf_turnstile_response"
                    action="comment"
                />

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
