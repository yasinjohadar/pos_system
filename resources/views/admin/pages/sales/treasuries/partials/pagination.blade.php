@if ($treasuries->hasPages())
    {{ $treasuries->withQueryString()->links() }}
@endif
