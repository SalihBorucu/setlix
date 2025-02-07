<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { DSButton, DSInput, DSForm, DSAlert } from '@/Components/UI'

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
                <img class="mx-auto h-12 w-auto" src="/logo.svg" alt="AI Setlist" />
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

            <!-- Social Registration -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-neutral-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-neutral-50 text-neutral-500">
                            Or continue with
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <DSButton variant="outline" size="lg" class="w-full">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <!-- Google Icon -->
                            <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                        </svg>
                        Google
                    </DSButton>

                    <DSButton variant="outline" size="lg" class="w-full">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <!-- GitHub Icon -->
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                        GitHub
                    </DSButton>
                </div>
            </div>
        </div>
    </main>
</template>
