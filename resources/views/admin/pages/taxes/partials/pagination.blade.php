@if ($taxes->hasPages())
    {{ $taxes->withQueryString()->links() }}
@endif
