<template>
  <AuthenticatedLayout>
    <Head title="Subscribe to Setlix" />
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <!-- Show error message if exists -->
      <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <!-- Trial Status -->
      <div v-if="trialDaysLeft > 0" class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-md">
        <p class="text-blue-700">
          You have {{ trialDaysLeft }} days left in your trial period for {{ band.name }}
        </p>
      </div>

      <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Left Column: Plan Details -->
        <div>
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Subscription Details</h2>
            
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
              <div>
                <p class="font-medium text-gray-900">{{ band.name }}</p>
                <p class="text-sm text-gray-500">Monthly Subscription</p>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-gray-900">$10.00</p>
                <p class="text-sm text-gray-500">per month</p>
              </div>
            </div>

            <ul class="space-y-3 mb-6">
              <li v-for="feature in features" :key="feature" class="flex items-start">
                <CheckCircleIcon class="h-5 w-5 text-green-500 flex-shrink-0" />
                <span class="ml-3 text-sm text-gray-600">{{ feature }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Right Column: Payment Form -->
        <StripePaymentForm
          :stripe-key="stripeKey"
          @success="handlePaymentSuccess"
          @error="handlePaymentError"
        />
      </div>

      <!-- Terms and Cancellation -->
      <div class="mt-6 text-center text-sm text-gray-500">
        <p>By subscribing, you agree to our Terms of Service. You can cancel anytime.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon } from '@heroicons/vue/24/solid';
import StripePaymentForm from '@/Components/UI/StripePaymentForm.vue';

const props = defineProps({
  band: {
    type: Object,
    required: true,
    validator: (band) => band.name && typeof band.name === 'string'
  },
  trialDaysLeft: {
    type: Number,
    required: true
  },
  stripeKey: {
    type: String,
    required: true
  }
});

const error = ref(null);

const features = [
  'Unlimited free band members',
  'Unlimited songs',
  'Unlimited setlists',
  'Full access for all band members',
  'Cloud backup and sync',
  'Performance mode',
  'File attachments support',
  'Priority email support'
];

const handlePaymentSuccess = (data) => {
  router.post(route('subscription.process'), {
    bandId: props.band.id,
    cardName: data.cardName
  }, {
    preserveScroll: true,
    onSuccess: (page) => {
      if (page.props.flash.success) {
        console.log('Success:', page.props.flash.success);
      }
    },
    onError: (errors) => {
      error.value = 'An error occurred during checkout. Please try again.';
      console.error('Checkout errors:', errors);
    }
  });
};

const handlePaymentError = (message) => {
  error.value = message;
};
</script> 