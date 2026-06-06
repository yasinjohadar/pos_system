@if ($categories->hasPages())
    {{ $categories->withQueryString()->links() }}
@endif
