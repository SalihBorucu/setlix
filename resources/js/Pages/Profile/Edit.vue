<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Profile Settings
                </h2>
                <p class="mt-1 text-sm text-neutral-500">
                    Manage your account settings and preferences
                </p>
            </div>
        </template>

        <div class="mx-auto max-w-7xl space-y-6">
            <UpdateProfileInformationForm
                :must-verify-email="mustVerifyEmail"
                :status="status"
            />

            <UpdatePasswordForm />

            <DeleteUserForm />
        </div>
    </AuthenticatedLayout>
</template>
