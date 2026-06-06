@if ($checks->hasPages())
    {{ $checks->withQueryString()->links() }}
@endif
