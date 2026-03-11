@extends('admin.layouts.master')

@section('page-title')
تعديل قائمة أسعار: {{ $priceList->name }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">تعديل قائمة أسعار</h4>
            <a href="{{ route('admin.price-lists.index') }}" class="btn btn-secondary">رجوع لقوائم الأسعار</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.price-lists.update', $priceList) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم القائمة</label>
                            <input type="text" name="name" value="{{ old('name', $priceList->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الوصف</label>
                            <input type="text" name="description" value="{{ old('description', $priceList->description) }}" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                                    {{ old('is_active', $priceList->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">نشطة</label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">أسعار المنتجات في هذه القائمة</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                            <tr>
                                <th style="width: 60%">المنتج</th>
                                <th style="width: 30%">سعر خاص</th>
                                <th style="width: 10%"></th>
                            </tr>
                            </thead>
                            <tbody id="price-list-items-body">
                            @php
                                $oldItems = old('items');
                                $items = $oldItems !== null ? $oldItems : $priceList->items->map(fn ($item) => [
                                    'product_id' => $item->product_id,
                                    'price' => $item->price,
                                ])->toArray();
                                if (empty($items)) {
                                    $items = [[]];
                                }
                            @endphp
                            @foreach($items as $index => $row)
                                <tr>
                                    <td>
                                        <select name="items[{{ $index }}][product_id]" class="form-select">
                                            <option value="">-- اختر منتجاً --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ (isset($row['product_id']) && (int)$row['product_id'] === $product->id) ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][price]" step="0.01" min="0"
                                               value="{{ $row['price'] ?? '' }}" class="form-control">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removePriceListRow(this)">حذف</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-outline-primary mb-3" onclick="addPriceListRow()">إضافة منتج آخر</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let priceListRowIndex = {{ count($items) }};

    function addPriceListRow() {
        const tbody = document.getElementById('price-list-items-body');
        const index = priceListRowIndex++;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${index}][product_id]" class="form-select">
                    <option value="">-- اختر منتجاً --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][price]" step="0.01" min="0" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removePriceListRow(this)">حذف</button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function removePriceListRow(button) {
        const row = button.closest('tr');
        if (row) {
            row.remove();
        }
    }
</script>
@endpush
@stop

