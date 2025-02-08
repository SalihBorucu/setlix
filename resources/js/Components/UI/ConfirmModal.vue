<script setup>
import { ref } from 'vue'
import { DSButton } from '@/Components/UI'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    confirmText: {
        type: String,
        default: 'Confirm'
    },
    cancelText: {
        type: String,
        default: 'Cancel'
    },
    confirmVariant: {
        type: String,
        default: 'danger'
    }
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const close = () => {
    emit('update:modelValue', false)
}

const confirm = () => {
    emit('confirm')
    close()
}
</script>

<template>
    <Modal :show="modelValue" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-neutral-900">
                {{ title }}
            </h2>

            <p class="mt-3 text-sm text-neutral-600">
                {{ description }}
            </p>

            <div class="mt-6 flex justify-end space-x-3">
                <DSButton
                    variant="outline"
                    @click="close"
                >
                    {{ cancelText }}
                </DSButton>

                <DSButton
                    :variant="confirmVariant"
                    @click="confirm"
                >
                    {{ confirmText }}
                </DSButton>
            </div>
        </div>
    </Modal>
</template> 