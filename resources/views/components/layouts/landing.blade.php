<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Setlix') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- End Meta Pixel Code -->
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/">
                                <img class="h-12 w-auto" src="/images/text_logo.svg" alt="Setlix" />
                            </a>
                        </div>
                        <div class="flex items-center">
                            <a href="{{ route('blog.index') }}" class="ml-8 text-gray-700 hover:text-gray-900">Blog</a>
                            <!-- Pricing and Support links: handle cross-page scroll/redirect; hidden on mobile -->
                            <a href="#pricing" class="ml-8 text-gray-700 hover:text-gray-900 hidden sm:inline" id="nav-pricing">Pricing</a>
                            <a href="#support" class="ml-8 text-gray-700 hover:text-gray-900 hidden sm:inline" id="nav-support">Support</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if (Route::has('login'))
                            <div class="space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Log in</a>
                                    <a href="{{ route('register') }}" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sky-500 hover:bg-sky-600">Sign up</a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Blog Showcase Section -->
        {{--
            This section displays the latest two blog posts.
            Make sure to pass a $latestBlogs variable (array of blog objects) to this view.
            Each blog should have: id, title, excerpt, and a route to its detail page.
        --}}
        @if(isset($latestBlogs) && count($latestBlogs) > 0)
        <section class="bg-white py-12 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Latest from our Blog</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($latestBlogs as $blog)
                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $blog->title }}</h3>
                                <p class="text-gray-600 mb-4">{{ $blog->excerpt }}</p>
                            </div>
                            <a href="{{ route('blog.show', $blog->id) }}" class="text-sky-600 hover:underline mt-auto">Read more &rarr;</a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 text-center">
                    <a href="{{ route('blog.index') }}" class="inline-block px-6 py-2 bg-sky-500 text-white rounded-md font-medium hover:bg-sky-600 transition">See all blogs</a>
                </div>
            </div>
        </section>
        @endif
        <!-- End Blog Showcase Section -->

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <!-- Social Links: Instagram, Facebook, TikTok -->
                <div class="flex justify-center space-x-6 mb-4">
                    <a href="https://tiktok.com/@setlix.app" target="_blank" rel="noopener" aria-label="TikTok" class="text-gray-400 hover:text-sky-500 transition">
                        <!-- TikTok SVG Icon -->
                        <svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.75 2h3.25a.75.75 0 0 1 .75.75v2.5a3.75 3.75 0 0 0 3.75 3.75h1a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-.75.75h-1.25A6.25 6.25 0 1 1 9.5 17.25v-3a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 .75.75v2.5a3.75 3.75 0 1 0 3.75-3.75h-1a.75.75 0 0 1-.75-.75v-7A.75.75 0 0 1 12.75 2z"/>
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="https://instagram.com/setlix.app" target="_blank" rel="noopener" aria-label="Instagram" class="text-gray-400 hover:text-sky-500 transition">
                        <!-- Instagram SVG Icon -->
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg">
                            <rect width="20" height="20" x="2" y="2" rx="5" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                            <circle cx="17" cy="7" r="1.5" fill="currentColor"/>
                        </svg>
                    </a>
                    <!-- Facebook -->
                    <a href="https://facebook.com/setlix" target="_blank" rel="noopener" aria-label="Facebook" class="text-gray-400 hover:text-sky-500 transition">
                        <!-- Facebook SVG Icon -->
                        <svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5 3.657 9.127 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.242 0-1.632.771-1.632 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.127 22 17 22 12"/>
                        </svg>
                    </a>
                </div>
                <!-- End Social Links -->
                <div class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Setlix. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
    <script>
    // Smooth scroll for anchor links if on landing page, otherwise redirect to landing with scrollTo param
    function handleNavScroll(section) {
        const onLanding = window.location.pathname === '/' || window.location.pathname === '/index';
        if (onLanding) {
            const target = document.getElementById(section);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        } else {
            window.location.href = '/?scrollTo=' + section;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers for Pricing and Support nav links
        document.getElementById('nav-pricing').addEventListener('click', function(e) {
            e.preventDefault();
            handleNavScroll('pricing');
        });
        document.getElementById('nav-support').addEventListener('click', function(e) {
            e.preventDefault();
            handleNavScroll('support');
        });
        // On landing, scroll to section if scrollTo param is present
        const params = new URLSearchParams(window.location.search);
        const scrollTo = params.get('scrollTo');
        if (scrollTo) {
            setTimeout(function() {
                const target = document.getElementById(scrollTo);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }, 200); // slight delay to ensure DOM is ready
        }
    });
    </script>

    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1452782776058339');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=1452782776058339&ev=PageView&noscript=1"
        /></noscript>
</body>
</html>
