@if ($priceLists->hasPages())
    {{ $priceLists->withQueryString()->links() }}
@endif
