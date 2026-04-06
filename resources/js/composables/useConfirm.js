import { ref } from 'vue';

export function useConfirm() {
    const confirmState = ref({
        open: false,
        title: 'Emin misin?',
        message: 'Bu işlem geri alınamaz.',
        onConfirm: null,
    });

    function confirm(message, onConfirm) {
        confirmState.value = {
            open: true,
            title: 'Emin misin?',
            message,
            onConfirm,
        };
    }

    function handleConfirm() {
        confirmState.value.onConfirm?.();
        confirmState.value.open = false;
    }

    function handleCancel() {
        confirmState.value.open = false;
    }

    return { confirmState, confirm, handleConfirm, handleCancel };
}
