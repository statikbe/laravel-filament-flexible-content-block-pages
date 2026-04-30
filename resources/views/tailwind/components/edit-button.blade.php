@once
    <link rel="stylesheet" href="{{ flexiblePagesAssetUrl('edit-button.css') }}">
@endonce

<a id="flexible-pages-edit-button"
   class="flexible-pages-edit-button"
   href="{{ $editUrl }}"
   aria-label="{{ flexiblePagesTrans('pages.toolbar.edit_page') }}">
    @svg('heroicon-o-pencil-square')
</a>

@once
    <script src="{{ flexiblePagesAssetUrl('edit-button.js') }}" defer></script>
@endonce

