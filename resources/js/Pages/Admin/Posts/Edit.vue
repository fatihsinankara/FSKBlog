<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MarkdownEditor from '@/Components/Admin/MarkdownEditor.vue';
import ImageUpload from '@/Components/Admin/ImageUpload.vue';
import TagInput from '@/Components/Admin/TagInput.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

const props = defineProps({
    post: Object,
    categories: Array,
    tags: Array,
});

const form = useForm({
    _method:            'PUT',
    title:              props.post.title,
    excerpt:            props.post.excerpt || '',
    body:               props.post.body,
    featured_image:     null,
    remove_featured_image: false,
    featured_image_alt: props.post.featured_image_alt || '',
    status:             props.post.status,
    category_id:        props.post.category_id || '',
    tag_ids:            props.post.tags?.map(t => t.id) ?? [],
    featured:           props.post.featured,
    published_at:       props.post.published_at ? props.post.published_at.substring(0, 16) : '',
    meta_title:         props.post.meta_title || '',
    meta_description:   props.post.meta_description || '',
});

function submit() {
    form.post(route('admin.posts.update', props.post.id), {
        forceFormData: true,
    });
}

function handleImageChange(value) {
    form.featured_image = value;
    form.remove_featured_image = value === null && !!props.post.featured_image_url;
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Düzenle: ${post.title}`" />

        <div class="flex items-center gap-4 mb-8">
            <Link :href="route('admin.posts.index')" class="p-2 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yazıyı Düzenle</h1>
                <p class="text-sm text-neutral-500 mt-0.5">{{ post.title }}</p>
            </div>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Başlık *</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full px-4 py-3 text-lg font-medium bg-transparent border-0 border-b border-neutral-200 dark:border-neutral-700 focus:outline-none focus:border-indigo-500 transition-colors text-neutral-900 dark:text-white"
                        :class="{ 'border-red-400': form.errors.title }"
                    />
                    <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>

                    <div class="mt-4">
                        <label class="block text-xs font-medium text-neutral-500 mb-1">Özet</label>
                        <textarea v-model="form.excerpt" rows="2" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" />
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">İçerik *</label>
                    <MarkdownEditor v-model="form.body" :error="form.errors.body" />
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4">SEO</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Meta Başlık</label>
                            <input v-model="form.meta_title" type="text" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Meta Açıklama</label>
                            <textarea v-model="form.meta_description" rows="2" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4">Yayın</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Durum</label>
                            <select v-model="form.status" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="draft">Taslak</option>
                                <option value="published">Yayınla</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-neutral-500 mb-1">Yayın Tarihi</label>
                            <input v-model="form.published_at" type="datetime-local" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.featured" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-xs text-neutral-600 dark:text-neutral-400">Öne çıkan yazı</span>
                        </label>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition-colors"
                    >
                        <Save :size="16" />
                        {{ form.processing ? 'Kaydediliyor...' : 'Güncelle' }}
                    </button>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-3">Kategori</h3>
                    <select v-model="form.category_id" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Kategorisiz</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-3">Taglar</h3>
                    <TagInput v-model="form.tag_ids" :tags="tags" />
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-3">Kapak Görseli</h3>
                    <ImageUpload :current-url="post.featured_image_url" :error="form.errors.featured_image" @change="handleImageChange" />
                    <input v-model="form.featured_image_alt" type="text" placeholder="Alt text" class="mt-2 w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
