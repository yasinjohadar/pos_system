<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ملخص أسبوعي</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; margin: 16px 0; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: right; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>ملخص المبيعات والمشتريات الأسبوعي</h2>
    <p>الفترة: من {{ $startDate }} إلى {{ $endDate }}</p>
    <table>
        <tr>
            <th>البند</th>
            <th>عدد الفواتير</th>
            <th>الإجمالي</th>
        </tr>
        <tr>
            <td>المبيعات</td>
            <td>{{ $salesCount }}</td>
            <td>{{ number_format($salesTotal, 2) }}</td>
        </tr>
        <tr>
            <td>المشتريات</td>
            <td>{{ $purchasesCount }}</td>
            <td>{{ number_format($purchasesTotal, 2) }}</td>
        </tr>
    </table>
    <p>تم إنشاء هذا الملخص تلقائياً من نظام نقطة البيع.</p>
</body>
</html>
