<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'opening_balance',
        'price_list_id',
        'segment_id',
        'loyalty_points',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'loyalty_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function segment()
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function saleInvoices()
    {
        return $this->hasMany(SaleInvoice::class, 'customer_id');
    }

    /**
     * إجمالي المبيعات (صافي الفواتير المؤكدة).
     */
    public function getTotalSalesAttribute()
    {
        return (float) $this->saleInvoices()->where('status', 'confirmed')->sum('total');
    }

    /**
     * إجمالي المرتجعات (من فواتير العميل المؤكدة، مرتجعات مكتملة فقط).
     */
    public function getTotalReturnsAttribute()
    {
        $invoiceIds = $this->saleInvoices()->where('status', 'confirmed')->pluck('id');
        return (float) SaleReturn::whereIn('sale_invoice_id', $invoiceIds)->where('status', SaleReturn::STATUS_COMPLETED)->sum('total_refund');
    }

    /**
     * إجمالي المدفوعات للعميل (من فواتيره المؤكدة).
     */
    public function getTotalPaidAttribute()
    {
        $invoiceIds = $this->saleInvoices()->where('status', 'confirmed')->pluck('id');
        return (float) SalePayment::whereIn('sale_invoice_id', $invoiceIds)->sum('amount');
    }

    /**
     * الرصيد المستحق = إجمالي المبيعات - المرتجعات - المدفوعات + رصيد افتتاحي.
     */
    public function getBalanceAttribute()
    {
        return $this->total_sales - $this->total_returns - $this->total_paid + (float) $this->opening_balance;
    }

    /**
     * حركات كشف الحساب: فواتير، مرتجعات، دفعات مرتبة بالتاريخ مع رصيد متراكم.
     * إذا وُجد as_of_date تُرجع الحركات حتى ذلك التاريخ فقط.
     *
     * @return \Illuminate\Support\Collection<int, array{date: \Carbon\Carbon, type: string, reference: string, description: string, debit: float, credit: float, balance: float}>
     */
    public function getStatementEntries(?\Carbon\Carbon $asOfDate = null): \Illuminate\Support\Collection
    {
        $entries = collect();
        $invoiceIds = $this->saleInvoices()->where('status', SaleInvoice::STATUS_CONFIRMED)->pluck('id');

        $invoices = $this->saleInvoices()
            ->where('status', SaleInvoice::STATUS_CONFIRMED)
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
                'description' => 'فاتورة بيع #' . $inv->number,
                'debit' => (float) $inv->total,
                'credit' => 0.0,
                'balance' => 0.0,
            ]);
        }

        $returns = SaleReturn::whereIn('sale_invoice_id', $invoiceIds)
            ->where('status', SaleReturn::STATUS_COMPLETED)
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
                'description' => 'مرتجع بيع #' . $ret->return_number,
                'debit' => 0.0,
                'credit' => (float) $ret->total_refund,
                'balance' => 0.0,
            ]);
        }

        $payments = SalePayment::whereIn('sale_invoice_id', $invoiceIds)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        foreach ($payments as $pay) {
            if ($asOfDate && $pay->payment_date->gt($asOfDate)) {
                continue;
            }
            $entries->push([
                'date' => $pay->payment_date,
                'type' => 'payment',
                'reference' => $pay->reference ?? ('دفعة #' . $pay->id),
                'description' => 'دفعة - فاتورة #' . $pay->saleInvoice->number,
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
