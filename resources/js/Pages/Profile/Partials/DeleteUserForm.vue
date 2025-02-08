<script setup>
import { DSButton, DSInput, DSAlertModal } from '@/Components/UI'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const deleteModal = ref(null)
const passwordInput = ref(null)

const form = useForm({
    password: '',
})

const confirmUserDeletion = () => {
    deleteModal.value.open()
}

const handleDelete = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    })
}

const closeModal = () => {
    form.clearErrors()
    form.reset()
}
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <DSButton variant="danger" @click="confirmUserDeletion">Delete Account</DSButton>

        <DSAlertModal
            ref="deleteModal"
            title="Delete Account"
            message="Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm."
            type="error"
            confirm-text="Delete Account"
            :show-cancel="true"
        >
            <div class="mt-4">
                <DSInput
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    label="Password"
                    :error="form.errors.password"
                    required
                    @keyup.enter="handleDelete"
                />
            </div>
        </DSAlertModal>
    </section>
</template>
