<script setup>
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Confirm Deletion'
    },
    message: {
        type: String,
        required: true
    },
    buttonText: {
        type: String,
        default: 'Delete'
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
            <p class="text-sm text-gray-500">
                {{ message }}
            </p>
        </div>

        <template #footer>
            <div class="mt-4 flex justify-end gap-3">
                <button
                    type="button"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    @click="close"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    @click="confirm"
                >
                    {{ buttonText }}
                </button>
            </div>
        </template>
    </Modal>
</template> 