@props(['formattedPrice', 'pricing'])

<section {{ $attributes->merge(['class' => 'py-12 bg-white']) }}>
    {{-- Allow passing id and other attributes for anchor navigation (e.g., id="pricing") --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                One Simple Price, No Surprises
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                <span class="line-through text-red-400">{{ $pricing['symbol'] }}{{ $pricing['amount'] * 2 }}</span>
                <span class="font-semibold text-gray-900">{{ $formattedPrice }}</span>/year per band. One flat fee covers your whole band—no extra costs.
            </p>
        </div>

        <div class="mt-12 max-w-lg mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-8">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900">Band Plan</h3>
                        <div class="mt-4 flex items-center justify-center">
                            <span class="text-2xl font-medium text-red-400 line-through mr-2">{{ $pricing['symbol'] }}{{ $pricing['amount'] * 2 }}</span>
                            <span class="text-4xl font-extrabold text-gray-900">{{ $formattedPrice }}</span>
                            <span class="ml-1 text-xl font-medium text-gray-500">/year</span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-green-600">50% OFF - Limited Time!</p>
                        <p class="mt-4 text-sm text-gray-500">Per band, unlimited members</p>
                    </div>
                    <div class="mt-8">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">Unlimited setlists</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">Unlimited songs</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">Unlimited band members</p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">Spotify integration</p>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('register') }}" class="block w-full bg-sky-500 border border-transparent rounded-md py-3 text-sm font-semibold text-white text-center hover:bg-sky-600">
                            Start Your Free Trial Today
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 