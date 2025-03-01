<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { DSButton, DSAlert } from '@/Components/UI';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Verify Email" />

    <main class="min-h-screen bg-neutral-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <ApplicationLogo />
                <h2 class="mt-6 text-3xl font-display font-bold text-neutral-900">
                    Verify your email
                </h2>
                <p class="mt-2 text-sm text-neutral-600">
                    Thanks for signing up! Please verify your email address to continue.
                </p>
            </div>

            <!-- Verification Notice -->
            <div class="space-y-6">
                <!-- Success Message -->
                <DSAlert
                    v-if="verificationLinkSent"
                    type="success"
                >
                    A new verification link has been sent to your email address.
                </DSAlert>

                <p class="text-sm text-neutral-600">
                    Before getting started, please verify your email address by clicking on the link we just emailed to you. 
                    If you didn't receive the email, we will gladly send you another one.
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <DSButton
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Sending...</span>
                        <span v-else>Resend Verification Email</span>
                    </DSButton>

                    <div class="text-center">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm font-medium text-neutral-600 hover:text-neutral-900"
                        >
                            Log Out
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </main>
</template>
