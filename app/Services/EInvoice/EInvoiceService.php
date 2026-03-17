<?php

namespace App\Services\EInvoice;

use App\Models\SaleInvoice;

class EInvoiceService
{
    /**
     * توليد هاش الفاتورة للتوافق مع الفوترة الإلكترونية.
     */
    public function getInvoiceHash(SaleInvoice $invoice): string
    {
        $payload = $invoice->id . '|' . $invoice->number . '|' . $invoice->invoice_date->format('Y-m-d') . '|' . (float) $invoice->total;
        return hash('sha256', $payload);
    }

    /**
     * التحقق من صحة بيانات الفاتورة للتصدير.
     */
    public function validateInvoice(SaleInvoice $invoice): array
    {
        $errors = [];
        if (empty($invoice->number)) {
            $errors[] = 'رقم الفاتورة مطلوب';
        }
        if ($invoice->total < 0) {
            $errors[] = 'إجمالي الفاتورة غير صالح';
        }
        return $errors;
    }

    /**
     * توليد XML مبسط للفاتورة (هيكل أساسي للتوافق المستقبلي).
     */
    public function generateXML(SaleInvoice $invoice): string
    {
        $hash = $this->getInvoiceHash($invoice);
        $customerName = $invoice->customer ? $invoice->customer->name : '';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Invoice>' . "\n";
        $xml .= '  <Number>' . htmlspecialchars($invoice->number) . '</Number>' . "\n";
        $xml .= '  <Date>' . $invoice->invoice_date->format('Y-m-d') . '</Date>' . "\n";
        $xml .= '  <Customer>' . htmlspecialchars($customerName) . '</Customer>' . "\n";
        $xml .= '  <Total>' . (float) $invoice->total . '</Total>' . "\n";
        $xml .= '  <Hash>' . $hash . '</Hash>' . "\n";
        $xml .= '</Invoice>';
        return $xml;
    }

    /**
     * بيانات QR للفاتورة (نص أو رابط يمكن تحويله لـ QR لاحقاً).
     */
    public function generateQRCode(SaleInvoice $invoice): string
    {
        $hash = $this->getInvoiceHash($invoice);
        return $invoice->number . '|' . $invoice->invoice_date->format('Y-m-d') . '|' . (float) $invoice->total . '|' . substr($hash, 0, 16);
    }
}
