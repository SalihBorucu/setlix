<template>
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
        <span v-else>{{ submitButtonText }}</span>
      </DSButton>
    </form>

    <!-- Security Notice -->
    <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
      <LockClosedIcon class="h-4 w-4 mr-1" />
      <span>Secure payment powered by Stripe</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { DSButton } from '@/Components/UI';
import { LockClosedIcon } from '@heroicons/vue/24/solid';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  stripeKey: {
    type: String,
    required: true
  },
  submitButtonText: {
    type: String,
    default: 'Subscribe Now • $10/month'
  },
  bandId: {
    type: [String, Number],
    required: true
  }
});

const emit = defineEmits(['success', 'error']);

const isLoading = ref(false);
const cardError = ref(null);
const cardName = ref('');
const stripe = ref(null);
const cardElement = ref(null);
const elements = ref(null);

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

  try {
    // Create subscription
    const { data } = await axios.post(route('subscription.create'), {
      band_id: props.bandId,
      card_name: cardName.value
    });

    // Confirm the payment with Stripe
    const { error, paymentIntent } = await stripe.value.confirmCardPayment(data.clientSecret, {
      payment_method: {
        card: elements.value.getElement('card'),
        billing_details: {
          name: cardName.value,
        },
      }
    });

    if (error) {
      throw new Error(error.message);
    }

    // Process the successful payment using Inertia's router
    router.post(route('bands.subscribe', props.bandId), {
      band_id: props.bandId,
      payment_intent: paymentIntent.id
    });

  } catch (e) {
    const errorMessage = e.message || 'An error occurred while processing your payment. Please try again.';
    emit('error', errorMessage);
    cardError.value = errorMessage;
  } finally {
    isLoading.value = false;
  }
};
</script> 