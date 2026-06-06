@if ($transfers->hasPages())
    {{ $transfers->withQueryString()->links() }}
@endif
