<script setup>
import {onMounted, ref} from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { DSButton, DSInput, DSForm, DSAlert } from '@/Components/UI'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Register" />

    <main class="min-h-screen bg-neutral-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <ApplicationLogo />
                <h2 class="mt-6 text-3xl font-display font-bold text-neutral-900">
                    Start your journey
                </h2>
                <p class="mt-2 text-sm text-neutral-600">
                    Already have an account?
                    <Link :href="route('login')" class="font-medium text-primary-500 hover:text-primary-600">
                        Sign in
                    </Link>
                </p>
            </div>

            <!-- Registration Form -->
            <DSForm @submit="submit" class="mt-8">
                <div class="space-y-6">
                    <!-- Error Message -->
                    <DSAlert
                        v-if="Object.keys(form.errors).length > 0"
                        type="error"
                    >
                        <ul class="list-disc list-inside">
                            <li v-for="error in form.errors" :key="error">{{ error }}</li>
                        </ul>
                    </DSAlert>

                    <!-- Name Input -->
                    <DSInput
                        type="text"
                        label="Full name"
                        v-model="form.name"
                        :error="form.errors.name"
                        required
                        autocomplete="name"
                    />

                    <!-- Email Input -->
                    <DSInput
                        type="email"
                        label="Email address"
                        v-model="form.email"
                        :error="form.errors.email"
                        required
                        autocomplete="email"
                    />

                    <!-- Password Input -->
                    <DSInput
                        type="password"
                        label="Password"
                        v-model="form.password"
                        :error="form.errors.password"
                        required
                        autocomplete="new-password"
                    />

                    <!-- Password Confirmation -->
                    <DSInput
                        type="password"
                        label="Confirm password"
                        v-model="form.password_confirmation"
                        :error="form.errors.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <!-- Terms and Conditions -->
                    <div class="text-sm text-neutral-600">
                        By registering, you agree to our
                        <a href="#" class="font-medium text-primary-500 hover:text-primary-600">Terms of Service</a>
                        and
                        <a href="#" class="font-medium text-primary-500 hover:text-primary-600">Privacy Policy</a>
                    </div>

                    <!-- Submit Button -->
                    <DSButton
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Creating account...</span>
                        <span v-else>Create account</span>
                    </DSButton>
                </div>
            </DSForm>
        </div>
    </main>
</template>
