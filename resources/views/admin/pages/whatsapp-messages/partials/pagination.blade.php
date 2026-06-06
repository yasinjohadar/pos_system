@if ($messages->hasPages())
    <div class="users-pagination">
        {{ $messages->links() }}
    </div>
@endif
