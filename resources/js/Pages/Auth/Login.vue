<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { DSButton, DSInput, DSForm, DSAlert } from '@/Components/UI'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Log in" />

    <main class="min-h-screen bg-neutral-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <ApplicationLogo />
                <h2 class="mt-6 text-3xl font-display font-bold text-neutral-900">
                    Welcome back
                </h2>
<!--                <p class="mt-2 text-sm text-neutral-600">-->
<!--                    Or-->
<!--                    <Link :href="route('register')" class="font-medium text-primary-500 hover:text-primary-600">-->
<!--                        Sign Up-->
<!--                    </Link>-->
<!--                </p>-->
            </div>

            <!-- Login Form -->
            <DSForm @submit="submit" class="mt-8">
                <div class="space-y-6">
                    <!-- Error Message -->
                    <DSAlert v-if="form.errors.email || form.errors.password" type="error">
                        <p v-if="form.errors.email">{{ form.errors.email }}</p>
                        <p v-if="form.errors.password">{{ form.errors.password }}</p>
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

                    <!-- Password Input -->
                    <DSInput
                        type="password"
                        label="Password"
                        v-model="form.password"
                        :error="form.errors.password"
                        required
                        autocomplete="current-password"
                    />

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 text-primary-500 focus:ring-primary-500 border-neutral-300 rounded"
                            />
                            <label for="remember" class="ml-2 block text-sm text-neutral-700">
                                Remember me
                            </label>
                        </div>

                        <Link
                            :href="route('password.request')"
                            class="text-sm font-medium text-primary-500 hover:text-primary-600"
                        >
                            Forgot your password?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <DSButton
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Signing in...</span>
                        <span v-else>Sign in</span>
                    </DSButton>
                </div>
            </DSForm>

        </div>
    </main>
</template>
