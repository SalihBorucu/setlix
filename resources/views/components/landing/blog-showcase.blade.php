{{-- Blog Showcase Section: Highlights the latest 2 blogs --}}
@if(isset($latestBlogs) && $latestBlogs->count() > 0)
    <section class="bg-white py-8 border-b border-gray-200 mb-10">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Latest from our Blog</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($latestBlogs->take(2) as $blog)
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $blog->title }}</h3>
                            <p class="text-gray-600 mb-3">{{ Str::limit($blog->content, 100) }}</p>
                        </div>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-sky-600 hover:underline mt-auto">Read more &rarr;</a>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 text-center">
                <a href="{{ route('blog.index') }}" class="inline-block px-5 py-2 bg-sky-500 text-white rounded-md font-medium hover:bg-sky-600 transition">See all stories</a>
            </div>
        </div>
    </section>
@endif
{{-- End Blog Showcase Section --}} 