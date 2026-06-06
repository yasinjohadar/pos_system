<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesQuote;
use App\Models\SalesQuoteItem;
use App\Models\SaleInvoice;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:sales-quote-list')->only('index');
        $this->middleware('permission:sales-quote-create')->only(['create', 'store']);
        $this->middleware('permission:sales-quote-show')->only('show');
        $this->middleware('permission:sales-quote-convert')->only('convert');
    }

    public function index(Request $request)
    {
        $quotes = SalesQuote::with(['customer', 'branch'])
            ->orderByDesc('quote_date')
            ->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.sales.quotes.partials.table-rows', compact('quotes'))->render(),
                'pagination' => view('admin.pages.sales.quotes.partials.pagination', compact('quotes'))->render(),
            ]);
        }

        return view('admin.pages.sales.quotes.index', compact('quotes'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.sales.quotes.create', compact('branches', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date',
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $quote = DB::transaction(function () use ($validated) {
            $quote = SalesQuote::create([
                'number' => SalesQuote::generateNumber(),
                'quote_date' => $validated['quote_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'branch_id' => $validated['branch_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'status' => SalesQuote::STATUS_DRAFT,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $total = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                SalesQuoteItem::create([
                    'sales_quote_id' => $quote->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $total,
                ]);
            }

            $quote->recalculateTotals();
            return $quote;
        });

        return redirect()->route('admin.sales-quotes.show', $quote)->with('success', 'تم إنشاء عرض السعر.');
    }

    public function show(SalesQuote $salesQuote)
    {
        $salesQuote->load(['items.product', 'customer', 'branch', 'warehouse']);
        return view('admin.pages.sales.quotes.show', compact('salesQuote'));
    }

    public function convert(SalesQuote $salesQuote)
    {
        if ($salesQuote->status === SalesQuote::STATUS_CONVERTED) {
            return back()->with('error', 'تم تحويل العرض مسبقاً.');
        }

        $salesQuote->load('items');

        $invoice = DB::transaction(function () use ($salesQuote) {
            $invoice = SaleInvoice::create([
                'number' => SaleInvoice::generateNumber($salesQuote->branch_id),
                'invoice_date' => now()->toDateString(),
                'branch_id' => $salesQuote->branch_id,
                'customer_id' => $salesQuote->customer_id,
                'warehouse_id' => $salesQuote->warehouse_id,
                'subtotal' => $salesQuote->subtotal,
                'tax_rate' => $salesQuote->tax_rate,
                'tax_amount' => $salesQuote->tax_amount,
                'discount_amount' => $salesQuote->discount_amount,
                'total' => $salesQuote->total,
                'payment_status' => SaleInvoice::PAYMENT_STATUS_PENDING,
                'status' => SaleInvoice::STATUS_DRAFT,
                'user_id' => auth()->id(),
                'notes' => 'من عرض سعر #' . $salesQuote->number,
            ]);

            foreach ($salesQuote->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $salesQuote->warehouse_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]);
            }

            $salesQuote->update([
                'status' => SalesQuote::STATUS_CONVERTED,
                'converted_invoice_id' => $invoice->id,
            ]);

            return $invoice;
        });

        return redirect()->route('admin.sale-invoices.show', $invoice)
            ->with('success', 'تم تحويل عرض السعر إلى فاتورة.');
    }
}
