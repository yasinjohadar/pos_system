@if ($branches->hasPages())
    {{ $branches->withQueryString()->links() }}
@endif
