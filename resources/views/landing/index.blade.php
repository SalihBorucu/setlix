<x-layouts.landing>
    <!-- Hero Section -->
    <x-landing.hero />

    <!-- Made by Musicians Tagline -->
    <x-landing.made-by-musicians />

    <!-- How It Works Section -->
    <x-landing.how-it-works />

    <!-- Who It's For Section -->
    <x-landing.who-its-for />

    <!-- Why Choose Setlix Section -->
    <x-landing.why-choose />

    <!-- Pricing Section -->
    <x-landing.pricing :formattedPrice="$formattedPrice" :pricing="$pricing" />

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

    <!-- Interactive Score Viewer Section -->
    <x-landing.score-viewer />

    <!-- See Setlix in Action Section -->
    <x-landing.see-in-action />

    <!-- Trusted by Musicians Section -->
    <x-landing.trusted-by-musicians />

    <!-- Support Section -->
    <section class="py-8 bg-sky-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-lg text-gray-700 font-semibold mb-2">Need help or have a question?</p>
            <p class="text-gray-600">Email us at <a href="mailto:setlix.app@gmail.com" class="text-sky-600 underline">setlix.app@gmail.com</a> and we'll get back to you as soon as we can!</p>
        </div>
    </section>
</x-layouts.landing> 