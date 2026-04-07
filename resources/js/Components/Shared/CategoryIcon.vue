<script setup>
import { computed } from 'vue';
import {
    BookOpen,
    Code2,
    Cpu,
    Database,
    Folder,
    Globe,
    Laptop2,
    PenTool,
    Rocket,
    Server,
    Shield,
    Tag,
    TerminalSquare,
    Wrench,
} from 'lucide-vue-next';

const props = defineProps({
    name: {
        type: String,
        default: '',
    },
    size: {
        type: [Number, String],
        default: 18,
    },
    strokeWidth: {
        type: [Number, String],
        default: 1.9,
    },
});

const iconMap = {
    book: BookOpen,
    'book-open': BookOpen,
    code: Code2,
    cpu: Cpu,
    database: Database,
    folder: Folder,
    globe: Globe,
    laptop: Laptop2,
    pen: PenTool,
    'pen-nib': PenTool,
    rocket: Rocket,
    server: Server,
    shield: Shield,
    tag: Tag,
    terminal: TerminalSquare,
    wrench: Wrench,
};

const resolvedIcon = computed(() => {
    const key = props.name.trim().toLowerCase();

    return iconMap[key] ?? Code2;
});
</script>

<template>
    <component
        :is="resolvedIcon"
        :size="size"
        :stroke-width="strokeWidth"
    />
</template>
