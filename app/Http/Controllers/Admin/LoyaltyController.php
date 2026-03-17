<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Services\Loyalty\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:loyalty-list')->only(['index']);
        $this->middleware('permission:loyalty-adjust')->only(['adjustForm', 'adjust']);
    }

    public function index(Request $request)
    {
        $query = LoyaltyTransaction::with('customer')
            ->orderByDesc('created_at');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $transactions = $query->paginate(25)->withQueryString();

        $customers = Customer::orderBy('name')->get(['id', 'name']);

        return view('admin.pages.sales.loyalty.index', compact('transactions', 'customers'));
    }

    public function adjustForm()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'loyalty_points']);
        return view('admin.pages.sales.loyalty.adjust', compact('customers'));
    }

    public function adjust(Request $request, LoyaltyService $loyaltyService)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'points' => 'required|integer',
            'reason' => 'required|string|max:500',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $loyaltyService->adjustPoints($customer, (int) $validated['points'], $validated['reason']);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'تم تعديل النقاط بنجاح.');
    }
}
