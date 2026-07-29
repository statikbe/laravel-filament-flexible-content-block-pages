@once
    <link rel="stylesheet" href="{{ flexiblePagesAssetUrl('edit-page-button.css') }}">
@endonce

<a id="flexible-pages_edit-page-button"
   class="flexible-pages_edit-page-button"
   href="{{ $editUrl }}"
   target="_blank"
   aria-label="{{ flexiblePagesTrans('pages.toolbar.edit_page') }}">
    @svg('heroicon-s-pencil')

    <span>{{ flexiblePagesTrans('pages.toolbar.edit_page') }}</span>
</a>
