<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import PostCardFeatured from '@/Components/Blog/PostCardFeatured.vue';
import Pagination from '@/Components/Shared/Pagination.vue';

defineProps({
    featured: Object,
    posts: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Anasayfa" />

        <section class="relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-[28rem] bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.16),_transparent_45%),radial-gradient(circle_at_top_right,_rgba(14,165,233,0.12),_transparent_38%)] pointer-events-none" />

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
                <header class="max-w-3xl mb-10 sm:mb-12">
                    <p class="text-[0.72rem] uppercase tracking-[0.24em] font-semibold text-neutral-500 dark:text-neutral-400 mb-4">
                        FSK Blog
                    </p>
                    <h1 class="font-serif text-[2.1rem] sm:text-[3rem] lg:text-[3.6rem] leading-[1.02] font-semibold text-neutral-950 dark:text-white max-w-4xl text-balance">
                        Yazılım, üretim süreci ve iyi internet estetiği üzerine notlar.
                    </h1>
                    <p class="mt-5 max-w-2xl text-[1.03rem] sm:text-[1.12rem] leading-8 text-neutral-600 dark:text-neutral-300">
                        Ağır olmayan ama karakterli bir blog deneyimi. Yeni yazılar, geliştirme notları ve bazen de ürün hissiyatına dair küçük gözlemler burada toplanıyor.
                    </p>
                </header>

                <!-- Featured post -->
                <div v-if="featured" class="mb-14">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <h2 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-[0.22em]">
                            Öne Çıkan Yazı
                        </h2>
                        <span class="hidden sm:inline text-xs text-neutral-400 dark:text-neutral-500">
                            Editörün seçimi
                        </span>
                    </div>
                    <PostCardFeatured :post="featured" />
                </div>

                <!-- Posts grid -->
                <div v-if="posts.data.length">
                    <h2 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-[0.22em] mb-6">
                        Son Yazılar
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
                    </div>
                    <Pagination :links="posts.links" />
                </div>

                <div v-else-if="featured" class="rounded-[1.5rem] border border-neutral-200 dark:border-neutral-800 bg-white/70 dark:bg-neutral-900/60 backdrop-blur-sm px-6 py-5 text-sm text-neutral-600 dark:text-neutral-300">
                    Şimdilik tüm odağı bu yazıya verdik. Yeni içerikler geldikçe burada liste halinde görünecekler.
                </div>

                <div v-else class="py-24 text-center">
                    <p class="text-lg text-neutral-500 dark:text-neutral-400">Henüz yazı yok.</p>
                    <p class="mt-2 text-sm text-neutral-400 dark:text-neutral-500">İlk içerik yayınlandığında burası dolmaya başlayacak.</p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
