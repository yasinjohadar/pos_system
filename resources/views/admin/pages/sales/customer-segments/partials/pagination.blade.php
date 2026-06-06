@if ($segments->hasPages())
    {{ $segments->withQueryString()->links() }}
@endif
