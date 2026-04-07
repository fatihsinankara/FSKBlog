<script setup>
import CategoryIcon from '@/Components/Shared/CategoryIcon.vue';
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import NewsletterSignup from '@/Components/Shared/NewsletterSignup.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';
import { Bell, BellOff } from 'lucide-vue-next';

const props = defineProps({
    category: Object,
    posts: Object,
    is_following: Boolean,
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

function toggleFollow() {
    router.post(route('categories.follow', props.category.id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <AppLayout>
        <SiteHead
            :title="category.name"
            :description="category.description || `${category.name} kategorisindeki yazılar`"
            :canonical="route('categories.show', category.slug)"
            :json-ld="{
                '@context': 'https://schema.org',
                '@type': 'CollectionPage',
                name: category.name,
                description: category.description,
                url: route('categories.show', category.slug),
            }"
        />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10 flex items-center justify-between gap-5">
                <!-- Image or icon -->
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0 shadow-sm">
                        <img v-if="category.image_url" :src="category.image_url" class="w-full h-full object-cover" :alt="category.name" />
                        <div v-else class="w-full h-full flex items-center justify-center"
                            :style="{ backgroundColor: category.color + '20', color: category.color }">
                            <CategoryIcon v-if="category.icon" :name="category.icon" :size="24" />
                            <span v-else class="w-4 h-4 rounded-full block" :style="{ backgroundColor: category.color }" />
                        </div>
                    </div>
                    <div>
                        <div
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mb-2"
                            :style="{ backgroundColor: category.color + '20', color: category.color }"
                        >
                            Kategori
                        </div>
                        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ category.name }}</h1>
                        <p v-if="category.description" class="text-neutral-500 dark:text-neutral-400 mt-1">{{ category.description }}</p>
                    </div>
                </div>
                <button
                    v-if="user"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium transition-colors"
                    :class="is_following
                        ? 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300'
                        : 'border-neutral-200 text-neutral-700 hover:border-neutral-300 dark:border-neutral-700 dark:text-neutral-300'"
                    @click="toggleFollow"
                >
                    <BellOff v-if="is_following" :size="15" />
                    <Bell v-else :size="15" />
                    {{ is_following ? 'Takibi Bırak' : 'Kategoriyi Takip Et' }}
                </button>
            </div>

            <div v-if="posts.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
            </div>
            <p v-else class="text-neutral-400 py-12 text-center">Bu kategoride henüz yazı yok.</p>

            <Pagination :links="posts.links" />
            <NewsletterSignup :category-id="category.id" title="Bu kategoriye abone ol" :description="`${category.name} kategorisinde yeni bir yazı yayınlandığında haber al.`" />
        </div>
    </AppLayout>
</template>
