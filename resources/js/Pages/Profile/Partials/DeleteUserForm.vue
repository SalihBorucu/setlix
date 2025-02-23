<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { DSButton, DSInput, DSCard, DSModal } from '@/Components/UI';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <DSCard class="max-w-2xl">
        <div class="p-6 space-y-6">
            <header>
                <h2 class="text-2xl font-bold text-neutral-900">
                    Delete Account
                </h2>
                <p class="mt-1 text-sm text-neutral-500">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>
            </header>

            <div class="flex justify-start">
                <DSButton
                    variant="danger"
                    @click="confirmUserDeletion"
                >
                    Delete Account
                </DSButton>
            </div>

            <DSModal
                :show="confirmingUserDeletion"
                @close="closeModal"
            >
                <div class="p-6">
                    <h2 class="text-lg font-medium text-neutral-900">
                        Are you sure you want to delete your account?
                    </h2>

                    <p class="mt-1 text-sm text-neutral-600">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please
                        enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <div class="mt-6">
                        <DSInput
                            v-model="form.password"
                            type="password"
                            label="Password"
                            ref="passwordInput"
                            :error="form.errors.password"
                            required
                            @keyup.enter="deleteUser"
                        />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <DSButton
                            variant="outline"
                            @click="closeModal"
                        >
                            Cancel
                        </DSButton>

                        <DSButton
                            variant="danger"
                            :disabled="form.processing"
                            @click="deleteUser"
                        >
                            <span v-if="form.processing">Deleting...</span>
                            <span v-else>Delete Account</span>
                        </DSButton>
                    </div>
                </div>
            </DSModal>
        </div>
    </DSCard>
</template>
