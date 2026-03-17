<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $saleInvoice->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 14px; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #333; }
        .header h1 { margin: 0; font-size: 22px; }
        .header .number { font-size: 18px; color: #555; }
        .info-table { width: 100%; margin-bottom: 24px; }
        .info-table td { padding: 4px 8px; vertical-align: top; }
        .info-table .label { color: #666; width: 120px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        table.items th { background: #f5f5f5; }
        table.totals { width: 100%; max-width: 320px; margin-right: auto; }
        table.totals td { padding: 6px 12px; }
        table.totals .total-row { font-weight: bold; font-size: 16px; border-top: 2px solid #333; }
        .footer { margin-top: 32px; text-align: center; font-size: 12px; color: #888; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button type="button" onclick="window.print();" style="padding: 8px 16px; cursor: pointer; font-size: 14px;">طباعة</button>
        <button type="button" onclick="window.close();" style="padding: 8px 16px; cursor: pointer; font-size: 14px; margin-right: 8px;">إغلاق</button>
    </div>

    <div class="header">
        <h1>فاتورة بيع</h1>
        <div class="number">رقم الفاتورة: {{ $saleInvoice->number }}</div>
        <div>التاريخ: {{ $saleInvoice->invoice_date->format('Y-m-d') }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">الفرع:</td>
            <td>{{ $saleInvoice->branch->name ?? '—' }}</td>
            <td class="label">العميل:</td>
            <td>{{ $saleInvoice->customer->name ?? 'عميل نقدي' }}</td>
        </tr>
        <tr>
            <td class="label">مخزن الصرف:</td>
            <td>{{ $saleInvoice->warehouse->name ?? '—' }}</td>
            <td class="label">الحالة:</td>
            <td>{{ $saleInvoice->status === \App\Models\SaleInvoice::STATUS_CONFIRMED ? 'مؤكدة' : ($saleInvoice->status === 'draft' ? 'مسودة' : 'ملغاة') }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($saleInvoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product->name ?? '—' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>المجموع الفرعي:</td><td>{{ number_format($saleInvoice->subtotal, 2) }}</td></tr>
        @if($saleInvoice->discount_amount > 0)
        <tr><td>الخصم@if($saleInvoice->coupon): (كوبون {{ $saleInvoice->coupon->code }})@endif:</td><td>- {{ number_format($saleInvoice->discount_amount, 2) }}</td></tr>
        @endif
        @if($saleInvoice->tax_amount > 0)
        <tr><td>الضريبة ({{ $saleInvoice->tax_rate }}%):</td><td>{{ number_format($saleInvoice->tax_amount, 2) }}</td></tr>
        @endif
        <tr class="total-row"><td>الإجمالي:</td><td>{{ number_format($saleInvoice->total, 2) }}</td></tr>
    </table>

    @if($saleInvoice->notes)
    <p style="margin-top: 16px;"><strong>ملاحظات:</strong> {{ $saleInvoice->notes }}</p>
    @endif

    <div class="footer" style="margin-top: 40px;">
        شكراً لتعاملكم
    </div>

    <script>
        // Optional: auto open print dialog when page loads
        // window.onload = function () { window.print(); };
    </script>
</body>
</html>
