<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { DSButton, DSInput, DSCard } from '@/Components/UI'

const form = useForm({
    name: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('profile.complete.update'), {
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Complete Profile Setup" />

    <GuestLayout>
        <DSCard class="p-6 w-full max-w-xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Welcome to Setlix!</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Please complete your profile to continue
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <DSInput
                        v-model="form.name"
                        type="text"
                        label="Full Name"
                        :error="form.errors.name"
                        required
                        autofocus
                    />
                    <p class="mt-1 text-sm text-gray-500">This is how you'll appear to other band members</p>
                </div>

                <div>
                    <DSInput
                        v-model="form.password"
                        type="password"
                        label="Choose a Password"
                        :error="form.errors.password"
                        required
                    />
                </div>

                <div>
                    <DSInput
                        v-model="form.password_confirmation"
                        type="password"
                        label="Confirm Password"
                        required
                    />
                </div>

                <div class="flex items-center justify-end">
                    <DSButton
                        type="submit"
                        variant="primary"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Setting up your account...</span>
                        <span v-else>Complete Setup & Continue</span>
                    </DSButton>
                </div>
            </form>
        </DSCard>
    </GuestLayout>
</template>
