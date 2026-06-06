@if ($products->hasPages())
    {{ $products->withQueryString()->links() }}
@endif
