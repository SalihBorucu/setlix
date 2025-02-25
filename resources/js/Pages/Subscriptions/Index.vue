<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton } from '@/Components/UI'
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'
import { ref } from 'vue'

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

// Add state for cancellation modal
const showCancelModal = ref(false)
const subscriptionToCancel = ref(null)

// Handle cancel button click
const handleCancelClick = (subscription) => {
    subscriptionToCancel.value = subscription
    showCancelModal.value = true
}

// Handle confirmation of cancellation
const handleConfirmCancel = () => {
    if (subscriptionToCancel.value) {
        window.Inertia.delete(route('subscriptions.cancel', subscriptionToCancel.value.id))
    }
}

// Format date to human readable format
const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(date)
}
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
            <p class="text-2xl font-bold text-primary-600">£{{ totalMonthlyFee.toFixed(2) }}/month</p>
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
                                    'text-red-600': subscription.status === 'expired',
                                    'text-orange-600': subscription.status === 'payment_failed',
                                    'text-gray-600': subscription.status === 'cancelled'
                                }">{{ subscription.status }}</span>
                            </p>
                            <p class="text-sm text-neutral-500" v-if="subscription.status === 'active'">
                                £{{ subscription.price }}/month
                            </p>
                            <!-- Show subscription end date for cancelled subscriptions -->
                            <p v-if="subscription.status === 'cancelled' && subscription.subscription_ends_at" 
                               class="text-sm text-neutral-500 mt-1">
                                Access until: <span class="font-medium">{{ formatDate(subscription.subscription_ends_at) }}</span>
                            </p>
                        </div>
                        
                        <div class="flex gap-3">
                            <!-- Subscribe button for expired/cancelled subscriptions -->
                            <Link
                                v-if="['expired', 'cancelled'].includes(subscription.status)"
                                :href="route('bands.subscribe', subscription.band.id)"
                            >
                                <DSButton variant="primary">
                                    Subscribe
                                </DSButton>
                            </Link>

                            <!-- Retry payment button for failed payments -->
                            <Link
                                v-if="subscription.status === 'payment_failed'"
                                :href="route('bands.subscribe', subscription.band.id)"
                            >
                                <DSButton variant="warning">
                                    Update Payment
                                </DSButton>
                            </Link>

                            <!-- Cancel button for active subscriptions -->
                            <DSButton
                                v-if="subscription.status === 'active'"
                                variant="danger"
                                @click="handleCancelClick(subscription)"
                            >
                                Cancel
                            </DSButton>

                            <!-- Trial badge -->
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

        <!-- Cancellation Confirmation Modal -->
        <ConfirmModal
            v-model="showCancelModal"
            title="Cancel Subscription"
            description="Are you sure you want to cancel this subscription? Your band will continue to have access until the end of the current billing period. After that, you'll lose access to premium features unless you resubscribe."
            confirm-text="Yes, Cancel Subscription"
            cancel-text="No, Keep Subscription"
            confirm-variant="danger"
            @confirm="handleConfirmCancel"
        />
    </AuthenticatedLayout>
</template> 