<script setup>
import { ImageOff, Save } from 'lucide-vue-next';
import { onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    form: Object,
    category: {
        type: Object,
        default: null,
    },
    submitLabel: String,
});

const imagePreview = ref(null);

function clearPreview() {
    if (imagePreview.value?.startsWith('blob:')) {
        URL.revokeObjectURL(imagePreview.value);
    }

    imagePreview.value = null;
}

function onImageChange(event) {
    const file = event.target.files?.[0];

    if (!file) {
        return;
    }

    clearPreview();
    props.form.image = file;

    if ('remove_image' in props.form) {
        props.form.remove_image = false;
    }

    imagePreview.value = URL.createObjectURL(file);
}

function removeImage() {
    clearPreview();
    props.form.image = null;

    if ('remove_image' in props.form) {
        props.form.remove_image = true;
    }
}

onBeforeUnmount(() => {
    clearPreview();
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Kategori Bilgileri</h2>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Ad *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Kategori adı"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            :class="{ 'border-red-400': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Açıklama</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Kategori açıklaması"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-[10rem_1fr]">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Renk</label>
                            <div class="flex items-center gap-3">
                                <input
                                    v-model="form.color"
                                    type="color"
                                    class="h-10 w-12 cursor-pointer rounded-lg border border-neutral-200 dark:border-neutral-700"
                                />
                                <span class="text-xs font-mono text-neutral-500">{{ form.color }}</span>
                            </div>
                            <p v-if="form.errors.color" class="mt-1 text-xs text-red-500">{{ form.errors.color }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">FA İkon</label>
                            <div class="flex items-center gap-3">
                                <input
                                    v-model="form.icon"
                                    type="text"
                                    placeholder="ör: code, laptop, pen-nib"
                                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                />
                                <div
                                    v-if="form.icon"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl"
                                    :style="{ backgroundColor: `${form.color}20`, color: form.color }"
                                >
                                    <font-awesome-icon :icon="['fas', form.icon]" />
                                </div>
                            </div>
                            <p v-if="form.errors.icon" class="mt-1 text-xs text-red-500">{{ form.errors.icon }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="mb-4 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Görsel</h2>

                <div class="space-y-4">
                    <div
                        v-if="imagePreview || (category?.image_url && !form.remove_image)"
                        class="overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700"
                    >
                        <img
                            :src="imagePreview || category?.image_url"
                            alt="Kategori görseli"
                            class="h-48 w-full object-cover"
                        />
                    </div>

                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-neutral-200 px-4 py-2.5 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                        Görsel Seç
                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="onImageChange"
                        />
                    </label>

                    <button
                        v-if="category?.image_url || imagePreview"
                        type="button"
                        class="inline-flex items-center gap-2 text-sm text-red-600 transition-colors hover:text-red-700"
                        @click="removeImage"
                    >
                        <ImageOff :size="16" />
                        Görseli kaldır
                    </button>

                    <p v-if="form.remove_image" class="text-xs text-amber-600 dark:text-amber-400">
                        Kaydettiğinde mevcut görsel kaldırılacak.
                    </p>

                    <p v-if="form.errors.image" class="text-xs text-red-500">{{ form.errors.image }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700 disabled:opacity-50"
                >
                    <Save :size="16" />
                    {{ form.processing ? 'Kaydediliyor...' : submitLabel }}
                </button>
            </div>
        </div>
    </div>
</template>
