@forelse ($rows as $p)
    <tr>
        <td><span class="users-user-name" style="cursor: default;">{{ $p->name }}</span></td>
        <td>
            @if ($p->category)
                <span class="users-badge users-badge--role">{{ $p->category->name }}</span>
            @else
                —
            @endif
        </td>
        <td>
            @if ($p->barcode)
                <span class="users-badge users-badge--role" dir="ltr">{{ $p->barcode }}</span>
            @else
                —
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="users-empty">لا توجد منتجات بدون مبيعات — جميع المنتجات النشطة لها مبيعات</td>
    </tr>
@endforelse

@if (count($rows) > 0)
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td colspan="2" class="text-end" style="padding: 0.875rem 1rem;">عدد المنتجات:</td>
        <td><span class="users-amount">{{ count($rows) }}</span></td>
    </tr>
@endif
