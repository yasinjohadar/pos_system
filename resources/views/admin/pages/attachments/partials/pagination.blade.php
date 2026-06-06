@if ($attachments->hasPages())
    {{ $attachments->withQueryString()->links() }}
@endif
