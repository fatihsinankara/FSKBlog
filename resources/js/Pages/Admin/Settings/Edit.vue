<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ImageUpload from '@/Components/Admin/ImageUpload.vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    site_name: props.settings.site_name || '',
    site_description: props.settings.site_description || '',
    site_keywords: props.settings.site_keywords || '',
    default_meta_title: props.settings.default_meta_title || '',
    default_meta_description: props.settings.default_meta_description || '',
    logo: null,
    favicon: null,
    default_og_image: null,
    custom_head_code: props.settings.custom_head_code || '',
    custom_body_end_code: props.settings.custom_body_end_code || '',
    maintenance_enabled: Boolean(props.settings.maintenance_enabled),
    maintenance_title: props.settings.maintenance_title || '',
    maintenance_message: props.settings.maintenance_message || '',
    remove_logo: false,
    remove_favicon: false,
    remove_default_og_image: false,
});

const currentLogo = computed(() => (form.remove_logo ? null : props.settings.logo_url));
const currentFavicon = computed(() => (form.remove_favicon ? null : props.settings.favicon_url));
const currentOgImage = computed(() => (form.remove_default_og_image ? null : props.settings.default_og_image_url));

function onLogo(file) {
    form.logo = file;
    form.remove_logo = !file;
}

function onFavicon(file) {
    form.favicon = file;
    form.remove_favicon = !file;
}

function onOgImage(file) {
    form.default_og_image = file;
    form.remove_default_og_image = !file;
}

function submit() {
    form.post(route('admin.settings.update'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Genel Ayarlar" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Genel Ayarlar</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Site başlığı, SEO varsayılanları, özel kod alanları ve bakım modu yönetimi.</p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Marka</h2>
                <div class="mt-5 grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Site başlığı</label>
                            <input v-model="form.site_name" type="text" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                            <p v-if="form.errors.site_name" class="mt-1 text-xs text-red-500">{{ form.errors.site_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Site açıklaması</label>
                            <textarea v-model="form.site_description" rows="3" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                            <p v-if="form.errors.site_description" class="mt-1 text-xs text-red-500">{{ form.errors.site_description }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Anahtar kelimeler</label>
                            <textarea v-model="form.site_keywords" rows="2" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                            <p v-if="form.errors.site_keywords" class="mt-1 text-xs text-red-500">{{ form.errors.site_keywords }}</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Logo</label>
                            <ImageUpload :current-url="currentLogo" :error="form.errors.logo" @change="onLogo" />
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Favicon</label>
                            <ImageUpload :current-url="currentFavicon" :error="form.errors.favicon" @change="onFavicon" />
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">SEO Varsayılanları</h2>
                <div class="mt-5 grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Varsayılan meta başlık</label>
                            <input v-model="form.default_meta_title" type="text" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Varsayılan meta açıklama</label>
                            <textarea v-model="form.default_meta_description" rows="4" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Varsayılan OG görseli</label>
                        <ImageUpload :current-url="currentOgImage" :error="form.errors.default_og_image" @change="onOgImage" />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Kod Alanları</h2>
                <div class="mt-5 grid gap-6 lg:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Head kodları</label>
                        <textarea v-model="form.custom_head_code" rows="8" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 font-mono text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                        <p class="mt-1 text-xs text-neutral-400">İzinli etiketler: meta, link, script, style, noscript.</p>
                        <p v-if="form.errors.custom_head_code" class="mt-1 text-xs text-red-500">{{ form.errors.custom_head_code }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Body sonu kodları</label>
                        <textarea v-model="form.custom_body_end_code" rows="8" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 font-mono text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                        <p class="mt-1 text-xs text-neutral-400">İzinli etiketler: script, noscript, iframe, div.</p>
                        <p v-if="form.errors.custom_body_end_code" class="mt-1 text-xs text-red-500">{{ form.errors.custom_body_end_code }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Bakım Modu</h2>
                <div class="mt-5 space-y-4">
                    <label class="inline-flex items-center gap-3 text-sm text-neutral-700 dark:text-neutral-300">
                        <input v-model="form.maintenance_enabled" type="checkbox" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                        Bakım modunu aktif et
                    </label>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Bakım başlığı</label>
                            <input v-model="form.maintenance_title" type="text" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-neutral-600 dark:text-neutral-400">Bakım mesajı</label>
                            <textarea v-model="form.maintenance_message" rows="3" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" :disabled="form.processing" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-60">
                    {{ form.processing ? 'Kaydediliyor...' : 'Ayarları Kaydet' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
