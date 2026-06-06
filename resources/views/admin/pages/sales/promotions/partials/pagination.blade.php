@if ($promotions->hasPages())
    {{ $promotions->withQueryString()->links() }}
@endif
