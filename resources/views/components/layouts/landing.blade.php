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
                            <a href="#pricing" class="ml-8 text-gray-700 hover:text-gray-900" id="nav-pricing">Pricing</a>
                            <a href="#support" class="ml-8 text-gray-700 hover:text-gray-900" id="nav-support">Support</a>
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
</body>
</html>
