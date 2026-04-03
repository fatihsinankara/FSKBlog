<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MarkdownEditor from '@/Components/Admin/MarkdownEditor.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

const form = useForm({
    title:            '',
    slug:             '',
    body:             '',
    status:           'draft',
    meta_title:       '',
    meta_description: '',
});

function submit() {
    form.post(route('admin.pages.store'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Yeni Sayfa" />

        <div class="flex items-center gap-4 mb-8">
            <Link
                :href="route('admin.pages.index')"
                class="p-2 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800"
            >
                <ArrowLeft :size="20" />
            </Link>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yeni Sayfa</h1>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Main content -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Title + slug -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Başlık *</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="Sayfa başlığı..."
                            class="w-full px-4 py-3 text-lg font-medium bg-transparent border-0 border-b border-neutral-200 dark:border-neutral-700 focus:outline-none focus:border-indigo-500 transition-colors text-neutral-900 dark:text-white placeholder-neutral-400"
                            :class="{ 'border-red-400': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">
                            Slug <span class="text-neutral-400">(boş bırakılırsa otomatik oluşturulur)</span>
                        </label>
                        <div class="flex items-center gap-1">
                            <span class="text-sm text-neutral-400 shrink-0">/p/</span>
                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="ornek-slug"
                                class="flex-1 px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                :class="{ 'border-red-400': form.errors.slug }"
                            />
                        </div>
                        <p v-if="form.errors.slug" class="text-xs text-red-500 mt-1">{{ form.errors.slug }}</p>
                    </div>
                </div>

                <!-- Body -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-3">İçerik</label>
                    <MarkdownEditor v-model="form.body" :error="form.errors.body" />
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Yayın</h3>
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">Durum</label>
                        <select
                            v-model="form.status"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="draft">Taslak</option>
                            <option value="published">Yayında</option>
                        </select>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition-colors"
                    >
                        <Save :size="16" />
                        Kaydet
                    </button>
                </div>

                <!-- SEO -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">SEO</h3>
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">Meta Başlık</label>
                        <input
                            v-model="form.meta_title"
                            type="text"
                            placeholder="SEO başlığı..."
                            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-1">Meta Açıklama</label>
                        <textarea
                            v-model="form.meta_description"
                            rows="3"
                            placeholder="SEO açıklaması..."
                            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                        />
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
