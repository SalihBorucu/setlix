<x-layouts.landing>
    <!-- Hero Section -->
    <x-landing.hero />

    <!-- How It Works Section -->
    <x-landing.how-it-works />

    <!-- Who It's For Section -->
    <x-landing.who-its-for />

    <!-- Why Choose Setlix Section -->
    <x-landing.why-choose />

    <!-- Pricing Section -->
    <x-landing.pricing />

    <!-- Get Started Section -->
    <section class="py-12 bg-sky-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Ready for a Smarter Setlist System?
                </h2>
                <p class="mt-4 text-lg text-gray-500">
                    No more lost papers. No more messy notes. Just a clean, organized way to run your gigs.
                </p>
                <div class="mt-8">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-sky-500 hover:bg-sky-600">
                        Sign Up Now
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing> 