<section class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">No More Lost Setlists.</span>
                        <span class="block text-sky-500">No More Chaos.</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Tired of printing new setlists for every gig, only to lose them or spill beer on them? With Setlix, your setlists, notes, and lyrics are always in one place—accessible on any device, anytime.
                    </p>
                    <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                        <div class="rounded-md shadow">
                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-sky-500 hover:bg-sky-600 md:py-4 md:text-lg md:px-10">
                                Start Your Free Trial
                            </a>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">
                        Start with a free trial for 1 band. Try all features with limited members, songs, and setlists.
                    </p>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="w-full object-cover sm:h-72 md:h-96 lg:w-[750px] lg:h-full lg:ml-10"
             src="{{ asset('images/hero-image.png') }}"
             alt="A worn-out, beer-stained paper setlist next to a clean, digital Setlix setlist on a phone/tablet">
    </div>
</section>
