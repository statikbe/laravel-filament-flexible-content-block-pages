@once
    <link rel="stylesheet" href="{{ flexiblePagesAssetUrl('edit-page-button.css') }}">
@endonce

<a id="flexible-pages_edit-page-button"
   class="flexible-pages_edit-page-button"
   href="{{ $editUrl }}"
   aria-label="{{ flexiblePagesTrans('pages.toolbar.edit_page') }}">
    @svg('heroicon-o-pencil-square')
</a>

@once
    <script src="{{ flexiblePagesAssetUrl('edit-page-button.js') }}" defer></script>
@endonce

