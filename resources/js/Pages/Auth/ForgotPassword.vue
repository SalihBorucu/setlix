<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { DSButton, DSInput, DSForm, DSAlert } from '@/Components/UI'

const form = useForm({
    email: '',
})

const status = ref(null)

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: (response) => {
            status.value = response?.props?.status
            form.reset('email')
        },
    })
}
</script>

<template>
    <Head title="Forgot Password" />

    <main class="min-h-screen bg-neutral-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <img class="mx-auto h-12 w-auto" src="/logo.svg" alt="AI Setlist" />
                <h2 class="mt-6 text-3xl font-display font-bold text-neutral-900">
                    Reset your password
                </h2>
                <p class="mt-2 text-sm text-neutral-600">
                    Remember your password?
                    <Link :href="route('login')" class="font-medium text-primary-500 hover:text-primary-600">
                        Sign in
                    </Link>
                </p>
            </div>

            <!-- Success Message -->
            <DSAlert v-if="status" type="success">
                {{ status }}
            </DSAlert>

            <!-- Reset Form -->
            <DSForm @submit="submit" class="mt-8">
                <div class="space-y-6">
                    <p class="text-sm text-neutral-600">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>

                    <!-- Error Message -->
                    <DSAlert v-if="form.errors.email" type="error">
                        {{ form.errors.email }}
                    </DSAlert>

                    <!-- Email Input -->
                    <DSInput
                        type="email"
                        label="Email address"
                        v-model="form.email"
                        :error="form.errors.email"
                        required
                        autocomplete="email"
                    />

                    <!-- Submit Button -->
                    <DSButton
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Sending reset link...</span>
                        <span v-else>Send password reset link</span>
                    </DSButton>
                </div>
            </DSForm>

            <!-- Help Section -->
            <div class="text-center mt-8">
                <p class="text-sm text-neutral-600">
                    Need help?
                    <a href="#" class="font-medium text-primary-500 hover:text-primary-600">
                        Contact support
                    </a>
                </p>
            </div>
        </div>
    </main>
</template>
