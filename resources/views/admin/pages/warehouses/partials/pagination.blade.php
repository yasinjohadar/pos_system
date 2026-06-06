@if ($warehouses->hasPages())
    {{ $warehouses->withQueryString()->links() }}
@endif
