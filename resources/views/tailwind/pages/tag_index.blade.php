<x-flexible-pages-base-layout wide="true">
    <header>
        <x-flexible-pages-language-switch/>
    </header>

    <main class="prose-headings:font-base">
        <div class="container mx-auto px-6 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-4">
                    {{ $tag->getTranslation('name', app()->getLocale()) }}
                </h1>

                @if($tag->seo_description)
                    <p class="text-lg text-gray-600 mb-6">
                        {{ $tag->getTranslation('seo_description', app()->getLocale()) }}
                    </p>
                @endif

                @if($contentCounts && count($contentCounts) > 0)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($contentCounts as $type => $count)
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">
                                {{ $count }} {{ $type }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($taggedContent->count() > 0)
                <div class="space-y-6">
                    @foreach($taggedContent as $item)
                        <article class="border-b border-gray-200 pb-6 last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm text-blue-600 font-medium uppercase">
                                    {{ $modelLabels[$item::class] ?? class_basename($item) }}
                                </span>
                                @if(method_exists($item, 'publishing_begins_at') && $item->publishing_begins_at)
                                    <time class="text-sm text-gray-500">
                                        {{ $item->publishing_begins_at->format('M j, Y') }}
                                    </time>
                                @endif
                            </div>

                            <h2 class="text-xl font-semibold mb-2">
                                @if(method_exists($item, 'getUrl'))
                                    <a href="{{ $item->getUrl() }}" class="hover:text-blue-600">
                                        {{ $item->title }}
                                    </a>
                                @else
                                    {{ $item->title }}
                                @endif
                            </h2>

                            @if(method_exists($item, 'intro') && $item->intro)
                                <p class="text-gray-600 mb-3">
                                    {{ Str::limit(strip_tags($item->intro), 200) }}
                                </p>
                            @endif

                            @if(method_exists($item, 'tags') && $item->tags->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->tags->take(5) as $itemTag)
                                        <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-700">
                                            {{ $itemTag->getTranslation('name', app()->getLocale()) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                @if($taggedContent->hasPages())
                    <div class="mt-8">
                        {{ $taggedContent->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg">
                        @lang('filament-flexible-content-block-pages::tag_pages.no_content', ['tag' => $tag->getTranslation('name', app()->getLocale())])
                    </p>
                </div>
            @endif
        </div>
    </main>

    <footer>
        <div>{{flexiblePagesSetting(Settings::SETTING_FOOTER_COPYRIGHT)}}</div>
    </footer>
</x-flexible-pages-base-layout>
