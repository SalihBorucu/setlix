<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { DSButton, DSInput, DSModal } from '@/Components/UI'

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    bandId: {
        type: Number,
        required: true
    }
})

const emit = defineEmits(['close'])

const form = useForm({
    email: '',
    role: 'member' // Default role
})

const roles = [
    { id: 'member', name: 'Member' },
    { id: 'admin', name: 'Admin' }
]

const submit = () => {
    form.post(route('bands.members.invite', props.bandId), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close')
            form.reset()
        }
    })
}
</script>

<template>
    <DSModal
        :show="show"
        @close="emit('close')"
    >
        <div class="sm:flex sm:items-start">
            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg font-medium leading-6 text-neutral-900">
                    Add Band Member
                </h3>
                <div class="mt-2">
                    <p class="text-sm text-neutral-500">
                        Send an invitation to join your band. They'll receive an email with instructions to accept.
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <DSInput
                v-model="form.email"
                type="email"
                label="Email Address"
                :error="form.errors.email"
                required
            />

            <div>
                <label class="block text-sm font-medium text-neutral-700 text-left">
                    Role
                </label>
                <div class="mt-1 space-y-2">
                    <div v-for="role in roles" :key="role.id" class="flex items-center">
                        <input
                            type="radio"
                            :id="role.id"
                            v-model="form.role"
                            :value="role.id"
                            class="h-4 w-4 border-neutral-300 text-primary-600 focus:ring-primary-500"
                        />
                        <label :for="role.id" class="ml-3 block text-sm font-medium text-neutral-700">
                            {{ role.name }}
                        </label>
                    </div>
                </div>
                <p v-if="form.errors.role" class="mt-2 text-sm text-error-600">
                    {{ form.errors.role }}
                </p>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
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
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Sending invitation...</span>
                    <span v-else>Send Invitation</span>
                </DSButton>
            </div>
        </form>
    </DSModal>
</template> 