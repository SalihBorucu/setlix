<x-layouts.landing>
    <div class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-bold mb-8 text-center">Blog</h1>
        <div class="grid gap-8">
            @foreach ($blogs as $blog)
                <div class="bg-white rounded-lg shadow p-6 flex flex-col md:flex-row items-center">
                    <img src="/images/{{ $blog->image_path }}" alt="{{ $blog->title }}"
                         class="sm:w-32 sm:h-32 object-cover rounded mb-4 md:mb-0 md:mr-6 border" loading="lazy"
                    >
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold mb-2">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="text-sky-600 hover:underline">{{ $blog->title }}</a>
                        </h2>
                        <p class="text-gray-600 mb-2">{{ Str::limit($blog->content, 120) }}</p>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-sky-500 hover:underline text-sm font-medium">Read more →</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.landing>
