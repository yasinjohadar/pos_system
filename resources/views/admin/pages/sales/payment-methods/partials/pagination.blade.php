@if ($paymentMethods->hasPages())
    {{ $paymentMethods->withQueryString()->links() }}
@endif
