@if ($vouchers->hasPages())
    {{ $vouchers->withQueryString()->links() }}
@endif
