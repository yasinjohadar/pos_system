@if ($quotes->hasPages())
    {{ $quotes->withQueryString()->links() }}
@endif
