<script setup>
import { useForm } from '@inertiajs/vue3';
import { DSButton, DSInput, DSCard } from '@/Components/UI';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <DSCard class="max-w-2xl">
        <div class="p-6 space-y-6">
            <header>
                <h2 class="text-2xl font-bold text-neutral-900">
                    Update Password
                </h2>
                <p class="mt-1 text-sm text-neutral-500">
                    Ensure your account is using a long, random password to stay secure.
                </p>
            </header>

            <form @submit.prevent="updatePassword" class="space-y-6">
                <div class="grid gap-4">
                    <DSInput
                        v-model="form.current_password"
                        type="password"
                        label="Current Password"
                        :error="form.errors.current_password"
                        required
                        autocomplete="current-password"
                    />

                    <DSInput
                        v-model="form.password"
                        type="password"
                        label="New Password"
                        :error="form.errors.password"
                        required
                        autocomplete="new-password"
                    />

                    <DSInput
                        v-model="form.password_confirmation"
                        type="password"
                        label="Confirm Password"
                        :error="form.errors.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <DSButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Password</span>
                    </DSButton>

                    <transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-if="form.recentlySuccessful"
                            class="text-sm text-neutral-600"
                        >
                            Saved.
                        </p>
                    </transition>
                </div>
            </form>
        </div>
    </DSCard>
</template>
