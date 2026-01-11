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
                <p class="text-sm text-gray-500">Yearly Subscription</p>
              </div>
              <div class="text-right">
                <p class="text-lg">
                  <span class="text-red-400 line-through mr-2">{{ pricing.symbol }}{{ pricing.amount * 2 }}</span>
                  <span class="font-bold text-gray-900">{{ formattedPrice }}</span>
                </p>
                <p class="text-sm text-gray-500">per year <span class="text-green-600 font-medium">50% OFF</span></p>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Information</h2>

          <form @submit.prevent="handleSubmit">
            <!-- Card Holder Name -->
            <div class="mb-4">
              <label for="cardName" class="block text-sm font-medium text-gray-700 mb-1">
                Card Holder Name
              </label>
              <input
                id="cardName"
                v-model="cardName"
                type="text"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
              />
            </div>

            <!-- Stripe Card Element -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Card Details
              </label>
              <div
                ref="cardElement"
                class="p-3 border rounded-md border-gray-300 bg-white"
              ></div>
              <div
                v-if="cardError"
                class="mt-1 text-sm text-red-600"
              >{{ cardError }}</div>
            </div>

            <!-- Submit Button -->
            <DSButton
              type="submit"
              variant="primary"
              class="w-full justify-center"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Processing...</span>
              <span v-else>Subscribe Now • {{ formattedPrice }}/year</span>
            </DSButton>
          </form>

          <!-- Security Notice -->
          <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
            <LockClosedIcon class="h-4 w-4 mr-1" />
            <span>Secure payment powered by Stripe</span>
          </div>
        </div>
      </div>

      <!-- Terms and Cancellation -->
      <div class="mt-6 text-center text-sm text-gray-500">
        <p>By subscribing, you agree to our Terms of Service. You can cancel anytime.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon, LockClosedIcon } from '@heroicons/vue/24/solid';
import { DSButton } from '@/Components/UI';
import axios from 'axios';

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
  },
  clientSecret: {
    type: String,
    required: true
  },
  formattedPrice: {
    type: String,
    required: true
  },
  pricing: {
    type: Object,
    required: true,
    default: () => ({})
  },
  priceId: {
    type: String,
    required: true
  }
});

// Debug log
console.log('Checkout props:', {
  band: props.band,
  pricing: props.pricing,
  priceId: props.priceId,
  formattedPrice: props.formattedPrice
});

const error = ref(null);
const cardError = ref(null);
const cardName = ref('');
const isLoading = ref(false);
const stripe = ref(null);
const cardElement = ref(null);
const elements = ref(null);

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

onMounted(() => {
  // Initialize Stripe
  stripe.value = Stripe(props.stripeKey);
  elements.value = stripe.value.elements();

  const card = elements.value.create('card', {
    style: {
      base: {
        fontSize: '16px',
        color: '#374151',
        '::placeholder': {
          color: '#9CA3AF',
        },
      },
    },
  });

  card.mount(cardElement.value);

  card.on('change', (event) => {
    cardError.value = event.error ? event.error.message : '';
  });
});

const handleSubmit = async () => {
  if (isLoading.value) return;

  isLoading.value = true;
  cardError.value = null;
  error.value = null;

  try {
    // Create payment method
    const { setupIntent, error: setupError } = await stripe.value.confirmCardSetup(
      props.clientSecret,
      {
        payment_method: {
          card: elements.value.getElement('card'),
          billing_details: {
            name: cardName.value,
          },
        }
      }
    );

    if (setupError) {
      throw new Error(setupError.message);
    }

    // Prepare subscription data
    const subscriptionData = {
      band_id: props.band.id,
      payment_method: setupIntent.payment_method,
      price_id: props.priceId
    };

    // Only add pricing details if they exist
    if (props.pricing?.currency) {
      subscriptionData.currency = props.pricing.currency;
      subscriptionData.amount = props.pricing.amount;
    }

    // Create subscription with the payment method
    const { data } = await axios.post(route('subscription.create'), subscriptionData);

    if (data.status === 'requires_action') {
      // Handle additional payment action if needed
      const { error: actionError } = await stripe.value.confirmCardPayment(data.payment_intent_client_secret);
      if (actionError) {
        throw new Error(actionError.message);
      }
    }

    // Redirect to subscription index on success
    router.visit(route('subscription.index'), {
      preserveScroll: true
    });

  } catch (e) {
    console.error('Subscription error:', e);
    error.value = e.response?.data?.message || e.message || 'An error occurred during checkout. Please try again.';
  } finally {
    isLoading.value = false;
  }
};
</script>
