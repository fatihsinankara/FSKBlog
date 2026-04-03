<script setup>
import { onMounted, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import PostMeta from '@/Components/Blog/PostMeta.vue';
import TagBadge from '@/Components/Blog/TagBadge.vue';
import CommentSection from '@/Components/Blog/CommentSection.vue';
import PostCollectionNav from '@/Components/Blog/PostCollectionNav.vue';
import ReadingProgress from '@/Components/Shared/ReadingProgress.vue';
import ShareButtons from '@/Components/Blog/ShareButtons.vue';
import TableOfContents from '@/Components/Blog/TableOfContents.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';
import { ArrowLeft, Bookmark, BookmarkCheck } from 'lucide-vue-next';

const props = defineProps({
    post:    Object,
    related: Array,
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const postUrl = computed(() => window.location.href);

function toggleBookmark() {
    router.post(route('bookmarks.toggle', props.post.id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
}

// Copy code butonlarını rendered body'e ekle
onMounted(() => {
    const blocks = document.querySelectorAll('.blog-prose pre');
    blocks.forEach((pre) => {
        if (pre.querySelector('.copy-btn')) return;

        pre.style.position = 'relative';

        const btn = document.createElement('button');
        btn.className = 'copy-btn';
        btn.setAttribute('aria-label', 'Kopyala');
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>`;

        btn.addEventListener('click', () => {
            const code = pre.querySelector('code')?.textContent ?? pre.textContent;
            navigator.clipboard.writeText(code).then(() => {
                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>`;
                    btn.classList.remove('copied');
                }, 2000);
            });
        });

        pre.appendChild(btn);
    });
});
</script>

<template>
    <AppLayout>
        <SiteHead
            :title="post.meta_title || post.title"
            :description="post.meta_description || post.excerpt"
            :image="post.featured_image_url"
            :canonical="route('posts.show', post.slug)"
            type="article"
            :published-at="post.published_at"
            :json-ld="{
                '@context': 'https://schema.org',
                '@type': 'Article',
                headline: post.title,
                description: post.meta_description || post.excerpt,
                datePublished: post.published_at,
                image: post.featured_image_url || undefined,
                author: post.user?.name ? { '@type': 'Person', name: post.user.name } : undefined,
                mainEntityOfPage: route('posts.show', post.slug),
            }"
        />

        <!-- Okuma ilerleme çubuğu -->
        <ReadingProgress />

        <article class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            <!-- Back link -->
            <Link :href="route('home')" class="inline-flex items-center gap-1.5 text-sm text-neutral-500 hover:text-neutral-900 dark:hover:text-white mb-8 transition-colors">
                <ArrowLeft :size="16" />
                Geri
            </Link>

            <header class="max-w-3xl mx-auto mb-10 sm:mb-12">
                <PostMeta :post="post" class="mb-5" />
                <h1 class="font-serif text-[2rem] sm:text-[3rem] lg:text-[3.35rem] font-semibold text-neutral-950 dark:text-white leading-[1.02] mb-5 text-balance">
                    {{ post.title }}
                </h1>
                <p v-if="post.excerpt" class="text-[1.05rem] sm:text-[1.16rem] text-neutral-600 dark:text-neutral-300 leading-8 max-w-2xl">
                    {{ post.excerpt }}
                </p>
                <div v-if="post.tags?.length" class="flex flex-wrap gap-1.5 mt-6">
                    <TagBadge v-for="tag in post.tags" :key="tag.id" :tag="tag" />
                </div>
            </header>

            <div v-if="post.featured_image_url" class="mb-10 sm:mb-14">
                <div class="relative rounded-[2rem] overflow-hidden aspect-[16/8.7] sm:aspect-[16/8] ring-1 ring-black/5 dark:ring-white/5 bg-neutral-100 dark:bg-neutral-900">
                    <img
                        :src="post.featured_image_url"
                        :alt="post.featured_image_alt || post.title"
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-white/10 dark:from-black/20 dark:to-white/5 pointer-events-none" />
                </div>
            </div>

            <PostCollectionNav :collection="post.collection" />

            <!-- İçerik + ToC yan yana (desktop) -->
            <div class="flex gap-12 items-start">
                <!-- Ana içerik -->
                <div class="min-w-0 flex-1 relative">
                    <div class="hidden sm:block absolute -left-10 top-2 h-24 w-px bg-gradient-to-b from-transparent via-neutral-300 to-transparent dark:via-neutral-700" />
                    <div
                        class="blog-prose prose prose-neutral dark:prose-invert prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-code:before:content-none prose-code:after:content-none max-w-none"
                        v-html="post.rendered_body"
                    />
                </div>

                <!-- Sağ sidebar: ToC (sadece desktop) -->
                <aside class="hidden xl:block w-56 shrink-0 sticky top-24 self-start">
                    <TableOfContents />
                </aside>
            </div>

            <!-- Paylaş + Kaydet -->
            <div class="max-w-2xl mx-auto mt-12 pt-8 border-t border-neutral-200 dark:border-neutral-800 flex flex-wrap items-center justify-between gap-4">
                <ShareButtons :title="post.title" :url="postUrl" />

                <!-- Kaydet butonu (giriş yapılmışsa) -->
                <button
                    v-if="user"
                    @click="toggleBookmark"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors"
                    :class="post.is_bookmarked
                        ? 'border-indigo-300 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/30'
                        : 'border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                >
                    <BookmarkCheck v-if="post.is_bookmarked" :size="13" />
                    <Bookmark v-else :size="13" />
                    {{ post.is_bookmarked ? (post.bookmark_status === 'reading' ? 'Okunuyor' : post.bookmark_status === 'completed' ? 'Tamamlandı' : 'Kaydedildi') : 'Kaydet' }}
                </button>
            </div>

            <!-- Yorumlar -->
            <CommentSection :post="post" />
        </article>

        <!-- İlgili Yazılar -->
        <div v-if="related.length" class="max-w-5xl mx-auto px-4 sm:px-6 pb-16">
            <div class="border-t border-neutral-200 dark:border-neutral-800 pt-12">
                <h2 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-6">
                    İlgili Yazılar
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <PostCard v-for="p in related" :key="p.id" :post="p" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.blog-prose pre {
    position: relative;
}

.copy-btn {
    position: absolute;
    top: 0.6rem;
    right: 0.6rem;
    padding: 0.35rem;
    border-radius: 0.4rem;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
    border: 1px solid rgba(255,255,255,0.15);
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s, color 0.15s, background 0.15s;
    line-height: 0;
}

.blog-prose pre:hover .copy-btn {
    opacity: 1;
}

.copy-btn:hover,
.copy-btn.copied {
    background: rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.95);
}

.copy-btn.copied {
    color: #86efac;
}
</style>
