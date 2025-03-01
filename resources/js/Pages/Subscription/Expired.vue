<template>
  <AppLayout title="Trial Expired">
    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Your Free Trial Has Expired
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
              <p>Subscribe now to continue using all features and keep your data.</p>
            </div>
            <div class="mt-5">
              <Link
                :href="route('subscription.checkout')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
              >
                Subscribe Now
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 