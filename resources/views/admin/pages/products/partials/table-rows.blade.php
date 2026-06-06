@forelse ($products as $product)
    <tr>
        <th scope="row" class="users-row-index">{{ $products->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-box"></i>
                    @endif
                </div>
                @can('product-show')
                    <a href="{{ route('admin.products.show', $product) }}" class="users-user-name">
                        {{ $product->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $product->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($product->barcode)
                <div class="users-email-cell">
                    <span class="users-email-link" dir="ltr">{{ $product->barcode }}</span>
                    <button type="button" class="users-copy-btn" data-copy="{{ $product->barcode }}"
                        data-copy-message="تم نسخ الباركود"
                        title="نسخ الباركود" aria-label="نسخ الباركود">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($product->category)
                @can('category-show')
                    <a href="{{ route('admin.categories.show', $product->category) }}" class="users-email-link">
                        {{ $product->category->name }}
                    </a>
                @else
                    <span>{{ $product->category->name }}</span>
                @endcan
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($product->unit)
                <span class="users-badge users-badge--role">{{ $product->unit->symbol ?? $product->unit->name }}</span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ number_format($product->base_price, 2) }}</span>
        </td>
        <td>
            @can('product-edit')
                <label class="users-toggle">
                    <input type="checkbox"
                        class="users-toggle-input"
                        data-toggle-url="{{ route('admin.products.toggle-status', $product) }}"
                        {{ $product->is_active ? 'checked' : '' }}>
                    <span class="users-toggle-track">
                        <span class="users-toggle-thumb"></span>
                    </span>
                    <span class="users-toggle-label">
                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </label>
            @else
                @if ($product->is_active)
                    <span class="users-badge users-badge--active">نشط</span>
                @else
                    <span class="users-badge users-badge--inactive">غير نشط</span>
                @endif
            @endcan
        </td>
        <td>
            <div class="users-actions">
                @can('product-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.products.show', $product) }}"
                        title="عرض المنتج">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('product-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.products.edit', $product) }}"
                        title="تعديل المنتج">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('product-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.products.destroy', $product) }}"
                        data-delete-title="حذف المنتج"
                        data-delete-message="هل أنت متأكد من حذف هذا المنتج؟"
                        data-delete-item="{{ $product->name }}"
                        title="حذف المنتج">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد منتجات</td>
    </tr>
@endforelse
