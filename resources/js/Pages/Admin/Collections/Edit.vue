<script setup>
import CollectionForm from '@/Components/Admin/CollectionForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    collection: Object,
    available_posts: Array,
});

function buildItems() {
    const assignedMap = new Map(
        (props.collection?.posts ?? []).map((post) => [
            post.id,
            post.pivot.part_number,
        ]),
    );

    return props.available_posts.map((post) => ({
        id: post.id,
        title: post.title,
        slug: post.slug,
        category_name: post.category?.name ?? null,
        published_at: post.published_at,
        selected: assignedMap.has(post.id),
        part_number: assignedMap.get(post.id) ?? '',
    })).sort((left, right) => {
        if (left.selected && right.selected) {
            return Number(left.part_number) - Number(right.part_number);
        }

        if (left.selected) return -1;
        if (right.selected) return 1;

        return left.title.localeCompare(right.title, 'tr');
    });
}

const form = useForm({
    title: props.collection.title,
    description: props.collection.description ?? '',
    status: props.collection.status,
    items: buildItems(),
});

function selectedItemsPayload() {
    return form.items
        .filter((item) => item.selected)
        .map((item) => ({
            post_id: item.id,
            part_number: Number(item.part_number),
        }));
}

function submit() {
    form.transform(() => ({
        title: form.title,
        description: form.description,
        status: form.status,
        items: selectedItemsPayload(),
    })).put(route('admin.collections.update', props.collection.id));
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Düzenle: ${collection.title}`" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.collections.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Koleksiyonu Düzenle</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ collection.title }}</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <CollectionForm :form="form" submit-label="Güncelle" />
        </form>
    </AdminLayout>
</template>
