<script setup>
import Modal from '@/Components/Modal.vue';
import { DSButton } from '@/Components/UI';
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    confirmText: {
        type: String,
        default: 'OK'
    },
    showCancel: {
        type: Boolean,
        default: false
    },
    cancelText: {
        type: String,
        default: 'Cancel'
    }
});

const emit = defineEmits(['confirm', 'cancel']);
const show = ref(false);

const open = () => {
    show.value = true;
};

const close = () => {
    show.value = false;
    emit('cancel');
};

const confirm = () => {
    emit('confirm');
    close();
};

defineExpose({ open });
</script>

<template>
    <Modal :show="show" :title="title" @close="close">
        <div class="mt-2">
            <p :class="[
                'text-sm',
                {
                    'text-success-600': type === 'success',
                    'text-error-600': type === 'error',
                    'text-warning-600': type === 'warning',
                    'text-neutral-600': type === 'info'
                }
            ]">
                {{ message }}
            </p>
        </div>

        <template #footer>
            <div class="mt-4 flex justify-end gap-3">
                <DSButton
                    v-if="showCancel"
                    variant="outline"
                    @click="close"
                >
                    {{ cancelText }}
                </DSButton>
                <DSButton
                    :variant="type === 'error' ? 'danger' : 'primary'"
                    @click="confirm"
                >
                    {{ confirmText }}
                </DSButton>
            </div>
        </template>
    </Modal>
</template> 