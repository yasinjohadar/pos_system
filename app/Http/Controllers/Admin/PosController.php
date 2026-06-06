<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\PosHeldSale;
use App\Models\Product;
use App\Models\Treasury;
use App\Models\Warehouse;
use App\Services\Pos\PosService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pos-access');
    }

    public function index(PosService $posService)
    {
        $shift = $posService->getOpenShift(auth()->id());
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $treasuries = Treasury::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();
        $heldSales = PosHeldSale::where('user_id', auth()->id())->latest()->limit(10)->get();

        return view('admin.pages.pos.index', compact(
            'shift', 'branches', 'warehouses', 'treasuries', 'paymentMethods', 'heldSales'
        ));
    }

    public function openShift(Request $request, PosService $posService)
    {
        $validated = $request->validate([
            'treasury_id' => 'required|exists:treasuries,id',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_cash' => 'required|numeric|min:0',
        ]);

        $shift = $posService->openShift(
            auth()->id(),
            (int) $validated['treasury_id'],
            (float) $validated['opening_cash'],
            $validated['branch_id'] ?? null
        );

        return redirect()->route('admin.pos.index')->with('success', 'تم فتح الوردية.');
    }

    public function closeShift(Request $request, PosService $posService)
    {
        $shift = $posService->getOpenShift(auth()->id());
        if (!$shift) {
            return back()->with('error', 'لا توجد وردية مفتوحة.');
        }

        $validated = $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $posService->closeShift($shift, (float) $validated['closing_cash'], $validated['notes'] ?? null);

        return redirect()->route('admin.pos.index')->with('success', 'تم إغلاق الوردية.');
    }

    public function searchProduct(Request $request)
    {
        $barcode = trim($request->input('barcode', ''));
        if ($barcode === '') {
            return response()->json(['product' => null]);
        }

        $product = Product::where('barcode', $barcode)->where('is_active', true)->first();
        if (!$product) {
            $product = Product::where('is_active', true)
                ->where(function ($q) use ($barcode) {
                    $q->where('name', 'like', "%{$barcode}%")
                        ->orWhereHas('barcodes', fn ($b) => $b->where('barcode', $barcode));
                })
                ->first();
        }

        if (!$product) {
            return response()->json(['product' => null]);
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'base_price' => (float) $product->base_price,
            ],
        ]);
    }

    public function hold(Request $request, PosService $posService)
    {
        $validated = $request->validate(['cart' => 'required|array']);
        $shift = $posService->getOpenShift(auth()->id());
        $held = $posService->holdSale(auth()->id(), $shift?->id, $validated['cart']);

        return response()->json(['reference' => $held->reference, 'id' => $held->id]);
    }

    public function resume(PosHeldSale $heldSale)
    {
        if ($heldSale->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json(['cart' => $heldSale->cart_data]);
    }

    public function checkout(Request $request, PosService $posService)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:customers,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method_id' => 'required_without:payments|exists:payment_methods,id',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'payments' => 'nullable|array',
            'payments.*.payment_method_id' => 'required_with:payments|exists:payment_methods,id',
            'payments.*.amount' => 'required_with:payments|numeric|min:0.01',
            'payments.*.treasury_id' => 'nullable|exists:treasuries,id',
        ]);

        try {
            $invoice = $posService->checkout($validated, auth()->id());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
            'print_url' => route('admin.sale-invoices.print', $invoice),
        ]);
    }
}
