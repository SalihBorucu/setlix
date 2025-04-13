<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
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
    pricing: {
        type: Object,
        required: true
    },
    formattedPrice: {
        type: String,
        required: true
    }
})

// Computed properties for subscription data
const computed = {
    totalMonthlyFee: (subscriptions) => {
        return subscriptions
            .filter(band => band.subscription?.status === 'active' && band.subscription?.is_owner)
            .reduce((total, band) => total + (band.subscription?.price || 0), 0)
    }
}

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
        router.delete(route('subscription.cancel', subscriptionToCancel.value.id))
    }
}

// Get subscription status for a band
const getBandStatus = (band) => {
    if (!band.subscription) return 'inactive'
    return band.subscription.status || 'inactive'
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
                Manage Band Subscriptions
            </h2>
        </template>

        <!-- Total Monthly Fee - Only show if user has any subscriptions they're paying for -->
        <div v-if="subscriptions.some(band => band.subscription?.is_owner)"
             class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-neutral-900 mb-2">Your Total Monthly Fee</h3>
            <p class="text-2xl font-bold text-primary-600">{{ pricing.symbol }}{{ computed.totalMonthlyFee(subscriptions).toFixed(2) }}/month</p>
        </div>

        <!-- Subscriptions List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <ul class="divide-y divide-neutral-200">
                <li v-for="band in subscriptions" :key="band.id" class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-medium text-neutral-900">
                                {{ band.name }}
                            </h4>
                            <p class="text-sm text-neutral-500">
                                Status: <span :class="{
                                    'text-green-600': getBandStatus(band) === 'active',
                                    'text-yellow-600': getBandStatus(band) === 'trialing',
                                    'text-red-600': getBandStatus(band) === 'inactive',
                                    'text-orange-600': getBandStatus(band) === 'payment_failed',
                                    'text-gray-600': getBandStatus(band) === 'cancelled'
                                }">{{ getBandStatus(band) }}</span>
                            </p>
                            <!-- Show price info based on subscription ownership -->
                            <template v-if="band.subscription">
                                <p v-if="band.subscription.is_owner"
                                   class="text-sm text-neutral-500">
                                    You are paying {{ band.subscription.formatted_price }}/month
                                </p>
                                <p v-else class="text-sm text-neutral-500">
                                    Subscription managed by {{ band.subscription.owner_name }}
                                </p>
                            </template>
                            <!-- Show subscription end date for cancelled subscriptions -->
                            <p v-if="getBandStatus(band) === 'cancelled' && band.subscription?.ends_at"
                               class="text-sm text-neutral-500 mt-1">
                                Access until: <span class="font-medium">{{ formatDate(band.subscription.ends_at) }}</span>
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <!-- Only show action buttons if user is the subscription owner or there's no subscription -->
                            <template v-if="!band.subscription || band.subscription.is_owner">
                                <!-- Subscribe button for inactive/cancelled subscriptions -->
                                <Link v-if="['inactive', 'cancelled'].includes(getBandStatus(band))"
                                      :href="route('subscription.checkout', band.id)"
                                >
                                    <DSButton variant="primary">
                                        Subscribe
                                    </DSButton>
                                </Link>

                                <!-- Retry payment button for failed payments -->
                                <Link
                                    v-if="getBandStatus(band) === 'payment_failed'"
                                    :href="route('subscription.checkout', band.id)"
                                >
                                    <DSButton variant="warning">
                                        Update Payment
                                    </DSButton>
                                </Link>

                                <!-- Cancel button for active subscriptions -->
                                <DSButton
                                    v-if="getBandStatus(band) === 'active'"
                                    variant="danger"
                                    @click="handleCancelClick(band)"
                                >
                                    Cancel
                                </DSButton>
                            </template>

                            <!-- Trial badge -->
                            <div
                                v-if="getBandStatus(band) === 'trialing'"
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
            description="Are you sure you want to cancel this subscription? Your band will continue to have access until the end of the billing period. After that, you'll lose access to premium features unless you resubscribe."
            confirm-text="Yes, Cancel Subscription"
            cancel-text="No, Keep Subscription"
            confirm-variant="danger"
            @confirm="handleConfirmCancel"
        />
    </AuthenticatedLayout>
</template>
