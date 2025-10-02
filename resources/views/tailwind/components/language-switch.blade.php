<nav class="relative inline-block text-right">
    <div class="py-1 flex" role="none">
        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <a href="{{$page && is_object($page) ? $page->getViewUrl($localeCode) : '#'}}"
               class="text-gray-700 block px-4 py-2 text-sm"
               role="menuitem"
               id="menu-item-{{$localeCode}}">
                {{ $properties['native'] }}
            </a>
        @endforeach
    </div>
</nav>
