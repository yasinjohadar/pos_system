@if ($entries->hasPages())
    {{ $entries->withQueryString()->links() }}
@endif
