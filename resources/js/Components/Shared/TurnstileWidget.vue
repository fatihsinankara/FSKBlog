<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    action: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const page = usePage();
const container = ref(null);
const widgetId = ref(null);
const scriptPromise = ref(null);
const turnstile = computed(() => page.props.turnstile ?? {});
const enabled = computed(() => Boolean(turnstile.value.enabled && turnstile.value.site_key));

function loadScript() {
    if (window.turnstile) {
        return Promise.resolve(window.turnstile);
    }

    if (scriptPromise.value) {
        return scriptPromise.value;
    }

    scriptPromise.value = new Promise((resolve, reject) => {
        const existing = document.getElementById('cf-turnstile-script');

        if (existing) {
            existing.addEventListener('load', () => resolve(window.turnstile), { once: true });
            existing.addEventListener('error', reject, { once: true });
            return;
        }

        const script = document.createElement('script');
        script.id = 'cf-turnstile-script';
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
        script.async = true;
        script.defer = true;
        script.onload = () => resolve(window.turnstile);
        script.onerror = reject;
        document.head.appendChild(script);
    });

    return scriptPromise.value;
}

async function renderWidget() {
    if (!enabled.value || widgetId.value !== null) {
        return;
    }

    await nextTick();

    if (!container.value) {
        return;
    }

    const api = await loadScript();

    widgetId.value = api.render(container.value, {
        sitekey: turnstile.value.site_key,
        action: props.action || undefined,
        callback: (token) => emit('update:modelValue', token),
        'expired-callback': () => emit('update:modelValue', ''),
        'error-callback': () => emit('update:modelValue', ''),
    });
}

function reset() {
    emit('update:modelValue', '');

    if (window.turnstile && widgetId.value !== null) {
        window.turnstile.reset(widgetId.value);
    }
}

onMounted(renderWidget);

watch(enabled, (isEnabled) => {
    if (isEnabled) {
        renderWidget();
    }
});

onBeforeUnmount(() => {
    if (window.turnstile && widgetId.value !== null) {
        window.turnstile.remove(widgetId.value);
    }
});

defineExpose({ reset });
</script>

<template>
    <div v-if="enabled" class="space-y-1">
        <div ref="container" />
        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
</template>
