<x-layouts.landing>
    {{-- SEO Meta Tags --}}
    @section('meta')
        <title>{{ $blog->title }} | Setlix Blog</title>
        <meta name="description" content="{{ Str::limit(strip_tags($blog->content), 150) }}">
        <meta property="og:title" content="{{ $blog->title }}" />
        <meta property="og:description" content="{{ Str::limit(strip_tags($blog->content), 150) }}" />
        <meta property="og:image" content="{{ url($blog->image_path) }}" />
    @endsection

    {{-- Make the blog card wider for better readability --}}
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="bg-white rounded-lg shadow p-10">
            <img src="/images/{{ $blog->image_path }}" alt="{{ $blog->title }}" class="w-full h-80 object-cover rounded mb-8 border" loading="lazy">
            <h1 class="text-4xl font-bold mb-6">{{ $blog->title }}</h1>
            <div class="prose max-w-none text-gray-800 text-lg">
                {!! nl2br(e($blog->content)) !!}
            </div>
            <a href="{{ route('blog.index') }}" class="inline-block mt-10 text-sky-500 hover:underline">← Back to Blog</a>
        </div>
    </div>
</x-layouts.landing>
