<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { DSButton, DSInput, DSCard, DSAlert } from '@/Components/UI';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <DSCard class="max-w-2xl">
        <div class="p-6 space-y-6">
            <header>
                <h2 class="text-2xl font-bold text-neutral-900">
                    Profile Information
                </h2>

                <p class="mt-1 text-sm text-neutral-500">
                    Update your account's profile information and email address.
                </p>
            </header>

            <!-- Error Message -->
            <DSAlert
                v-if="Object.keys(form.errors).length > 0"
                type="error"
            >
                <ul class="list-disc list-inside">
                    <li v-for="error in form.errors" :key="error">{{ error }}</li>
                </ul>
            </DSAlert>

            <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
                <div class="grid gap-4">
                    <!-- Name -->
                    <DSInput
                        v-model="form.name"
                        type="text"
                        label="Name"
                        :error="form.errors.name"
                        required
                        autofocus
                    />

                    <!-- Email -->
                    <DSInput
                        v-model="form.email"
                        type="email"
                        label="Email"
                        :error="form.errors.email"
                        required
                    />
                </div>

                <!-- Email Verification Notice -->
                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="text-sm text-neutral-800">
                        Your email address is unverified.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="text-primary-600 hover:text-primary-700 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600"
                    >
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <DSButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>Save Changes</span>
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
