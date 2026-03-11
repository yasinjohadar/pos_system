<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'opening_balance',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }

    /** إجمالي المشتريات (صافي الفواتير المؤكدة) */
    public function getTotalPurchasesAttribute()
    {
        return (float) $this->purchaseInvoices()->where('status', 'confirmed')->sum('total');
    }

    /** إجمالي المرتجعات (من فواتير المورد المؤكدة، مرتجعات مكتملة فقط) */
    public function getTotalReturnsAttribute()
    {
        $invoiceIds = $this->purchaseInvoices()->where('status', PurchaseInvoice::STATUS_CONFIRMED)->pluck('id');
        return (float) PurchaseReturn::whereIn('purchase_invoice_id', $invoiceIds)
            ->where('status', PurchaseReturn::STATUS_COMPLETED)
            ->sum('total_refund');
    }

    /** إجمالي المدفوعات للمورد */
    public function getTotalPaidAttribute()
    {
        return (float) $this->payments()->sum('amount');
    }

    /** الرصيد المستحق للمورد = إجمالي المشتريات - المرتجعات - المدفوعات + رصيد افتتاحي */
    public function getBalanceAttribute()
    {
        return $this->total_purchases - $this->total_returns - $this->total_paid + (float) $this->opening_balance;
    }

    /**
     * حركات كشف الحساب: فواتير شراء، مرتجعات، دفعات مرتبة بالتاريخ مع رصيد متراكم.
     * إذا وُجد asOfDate تُرجع الحركات حتى ذلك التاريخ فقط.
     *
     * @return \Illuminate\Support\Collection<int, array{date: \Carbon\Carbon, type: string, reference: string, description: string, debit: float, credit: float, balance: float}>
     */
    public function getStatementEntries(?\Carbon\Carbon $asOfDate = null): \Illuminate\Support\Collection
    {
        $entries = collect();
        $invoiceIds = $this->purchaseInvoices()->where('status', PurchaseInvoice::STATUS_CONFIRMED)->pluck('id');

        $invoices = $this->purchaseInvoices()
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED)
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();
        foreach ($invoices as $inv) {
            if ($asOfDate && $inv->invoice_date->gt($asOfDate)) {
                continue;
            }
            $entries->push([
                'date' => $inv->invoice_date,
                'type' => 'invoice',
                'reference' => $inv->number,
                'description' => 'فاتورة شراء #' . $inv->number,
                'debit' => (float) $inv->total,
                'credit' => 0.0,
                'balance' => 0.0,
            ]);
        }

        $returns = PurchaseReturn::whereIn('purchase_invoice_id', $invoiceIds)
            ->where('status', PurchaseReturn::STATUS_COMPLETED)
            ->orderBy('return_date')
            ->orderBy('id')
            ->get();

        foreach ($returns as $ret) {
            if ($asOfDate && $ret->return_date->gt($asOfDate)) {
                continue;
            }
            $entries->push([
                'date' => $ret->return_date,
                'type' => 'return',
                'reference' => $ret->return_number,
                'description' => 'مرتجع شراء #' . $ret->return_number,
                'debit' => 0.0,
                'credit' => (float) $ret->total_refund,
                'balance' => 0.0,
            ]);
        }

        $payments = $this->payments()
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        foreach ($payments as $pay) {
            if ($asOfDate && $pay->payment_date->gt($asOfDate)) {
                continue;
            }
            $desc = $pay->purchase_invoice_id
                ? 'دفعة - فاتورة #' . ($pay->purchaseInvoice->number ?? '')
                : ('دفعة #' . $pay->id);
            $entries->push([
                'date' => $pay->payment_date,
                'type' => 'payment',
                'reference' => $pay->reference ?? ('دفعة #' . $pay->id),
                'description' => $desc,
                'debit' => 0.0,
                'credit' => (float) $pay->amount,
                'balance' => 0.0,
            ]);
        }

        $entries = $entries->sortBy('date')->values();

        $balance = (float) $this->opening_balance;
        foreach ($entries as $i => $e) {
            $balance += $e['debit'] - $e['credit'];
            $entries[$i]['balance'] = round($balance, 2);
        }

        return $entries;
    }
}
