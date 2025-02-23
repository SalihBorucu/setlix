<template>
  <div v-if="showBanner" 
       class="bg-blue-50 border-b border-blue-200 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <span class="text-blue-700" v-if="remainingDays > 0">
          {{ remainingDays }} days remaining in your free trial
        </span>
        <span class="text-red-700" v-else>
          Your trial has expired
        </span>
      </div>
      
      <DSButton
        v-if="!isSubscribed"
        variant="primary"
        :href="route('subscription.checkout')"
      >
        Subscribe Now
      </DSButton>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import DSButton from '@/Components/UI/DSButton.vue';

const page = usePage();
const trial = computed(() => page.props.trial);
const isSubscribed = computed(() => page.props.auth.user.is_subscribed);
const remainingDays = computed(() => trial.value.remainingDays);
const showBanner = computed(() => !isSubscribed.value && trial.value.isInTrial);
</script> 