<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton } from '@/Components/UI'

// Props received from the controller
defineProps({
    subscriptions: {
        type: Array,
        required: true
    },
    totalMonthlyFee: {
        type: Number,
        required: true
    }
})
</script>

<template>
    <Head title="Subscriptions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-neutral-900">
                Manage Subscriptions
            </h2>
        </template>

        <!-- Total Monthly Fee -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-neutral-900 mb-2">Total Monthly Fee</h3>
            <p class="text-2xl font-bold text-primary-600">${{ totalMonthlyFee.toFixed(2) }}/month</p>
        </div>

        <!-- Subscriptions List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <ul class="divide-y divide-neutral-200">
                <li v-for="subscription in subscriptions" :key="subscription.id" class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-medium text-neutral-900">
                                {{ subscription.band.name }}
                            </h4>
                            <p class="text-sm text-neutral-500">
                                Status: <span :class="{
                                    'text-green-600': subscription.status === 'active',
                                    'text-yellow-600': subscription.status === 'trialing',
                                    'text-red-600': subscription.status === 'expired' || subscription.status === 'no subscription'
                                }">{{ subscription.status }}</span>
                            </p>
                            <p class="text-sm text-neutral-500" v-if="subscription.status === 'active'">
                                ${{ subscription.price }}/month
                            </p>
                        </div>
                        
                        <div class="flex gap-3">
                            <!-- Wrap DSButton in Link component for navigation -->
                            <Link
                                v-if="subscription.status === 'expired' || subscription.status === 'no subscription'"
                                :href="route('bands.subscribe', subscription.band.id)"
                            >
                                <DSButton variant="primary">
                                    Subscribe
                                </DSButton>
                            </Link>

                            <!-- Show Cancel button for active subscriptions -->
                            <DSButton
                                v-if="subscription.status === 'active'"
                                variant="danger"
                                @click="$inertia.delete(route('subscriptions.cancel', subscription.id))"
                            >
                                Cancel Subscription
                            </DSButton>

                            <!-- Show "On Trial" badge for trial period -->
                            <div
                                v-if="subscription.status === 'trialing'"
                                class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium"
                            >
                                Trial Active
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template> 