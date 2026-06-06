<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند {{ $cashVoucher->voucher_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 14px; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #333; }
        .header h1 { margin: 0; font-size: 22px; }
        .header .company { font-size: 16px; color: #555; margin-top: 4px; }
        .info-table { width: 100%; margin-bottom: 24px; }
        .info-table td { padding: 6px 8px; vertical-align: top; }
        .info-table .label { color: #666; width: 140px; font-weight: 600; }
        .amount-box { text-align: center; padding: 16px; border: 2px solid #333; margin: 24px auto; max-width: 400px; }
        .amount-box .label { font-size: 14px; color: #666; }
        .amount-box .value { font-size: 24px; font-weight: bold; margin-top: 8px; }
        .footer { margin-top: 32px; text-align: center; font-size: 12px; color: #888; }
        .signatures { display: flex; justify-content: space-between; margin-top: 48px; }
        .signatures div { text-align: center; width: 30%; border-top: 1px solid #999; padding-top: 8px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button type="button" onclick="window.print();" style="padding: 8px 16px; cursor: pointer;">طباعة</button>
        <button type="button" onclick="window.close();" style="padding: 8px 16px; cursor: pointer; margin-right: 8px;">إغلاق</button>
    </div>

    <div class="header">
        <h1>سند {{ $cashVoucher->type === 'receipt' ? 'قبض' : 'صرف' }}</h1>
        @if (!empty($companySettings['company_name']))
            <div class="company">{{ $companySettings['company_name'] }}</div>
        @endif
        <div style="margin-top: 8px;">رقم السند: {{ $cashVoucher->voucher_number }}</div>
        <div>التاريخ: {{ $cashVoucher->date->format('Y-m-d') }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">الخزنة / البنك:</td>
            <td>
                @if ($cashVoucher->treasury)
                    {{ $cashVoucher->treasury->name }}
                @elseif ($cashVoucher->bankAccount)
                    {{ $cashVoucher->bankAccount->name }}
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">الفئة:</td>
            <td>{{ $cashVoucher->category ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">الوصف:</td>
            <td>{{ $cashVoucher->description ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">المستخدم:</td>
            <td>{{ $cashVoucher->user->name ?? '—' }}</td>
        </tr>
        @if ($cashVoucher->notes)
            <tr>
                <td class="label">ملاحظات:</td>
                <td>{{ $cashVoucher->notes }}</td>
            </tr>
        @endif
    </table>

    <div class="amount-box">
        <div class="label">المبلغ {{ $cashVoucher->type === 'receipt' ? 'المقبوض' : 'المصروف' }}</div>
        <div class="value">{{ number_format($cashVoucher->amount, 2) }} {{ $cashVoucher->currency ?? ($companySettings['default_currency'] ?? '') }}</div>
    </div>

    <div class="signatures">
        <div>المستلم</div>
        <div>المحاسب</div>
        <div>المدير</div>
    </div>

    @if (!empty($companySettings['invoice_footer']))
        <div class="footer">{{ $companySettings['invoice_footer'] }}</div>
    @endif
</body>
</html>
