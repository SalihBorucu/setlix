<script setup>
import {computed, ref} from 'vue'
import { DSButton, DSInput, DSDurationInput } from '@/Components/UI'

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    }
})

const emit = defineEmits(['close', 'create'])

const form = ref({
    title: '',
    duration: '', // For the display value
    duration_seconds: '', // For the actual value
    notes: ''
})

const submit = () => {
    emit('create', {
        type: 'break',
        title: form.value.title,
        duration_seconds: form.value.duration_seconds,
        notes: form.value.notes
    })
    form.value = { title: '', duration: '', duration_seconds: '', notes: '' }
    emit('close')
}

const buttonEnabled = computed(() => {
    return form.value.title && form.value.duration_seconds
})
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 bg-neutral-900/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-medium text-neutral-900 mb-4">Add Break</h3>

            <form @submit.prevent="submit" class="space-y-4">
                <DSInput
                    v-model="form.title"
                    label="Title"
                    placeholder="e.g., Merch Break, Intermission"
                    required
                />

                <DSDurationInput
                    v-model="form.duration"
                    @update:seconds="(seconds) => form.duration_seconds = seconds"
                    label="Duration"
                    required
                />

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        placeholder="Add any notes or announcements for this break..."
                        class="block w-full rounded-lg border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>

                <div class="flex justify-end space-x-3">
                    <DSButton
                        type="button"
                        variant="outline"
                        @click="emit('close')"
                    >
                        Cancel
                    </DSButton>
                    <DSButton
                        type="submit"
                        variant="primary"
                        :disabled="!buttonEnabled"
                    >
                        Add Break
                    </DSButton>
                </div>
            </form>
        </div>
    </div>
</template>
