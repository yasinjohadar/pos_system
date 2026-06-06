<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Settings\CompanySettingsService;
use App\Models\Tax;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:settings-manage');
    }

    public function index(CompanySettingsService $service)
    {
        $settings = $service->getSettings();
        $taxes = Tax::where('is_active', true)->orderBy('name')->get();
        $logoUrl = $service->logoUrl();

        return view('admin.pages.settings.company.index', compact('settings', 'taxes', 'logoUrl'));
    }

    public function update(Request $request, CompanySettingsService $service)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'default_currency' => 'required|string|max:10',
            'invoice_footer' => 'nullable|string|max:1000',
            'default_tax_id' => 'nullable|exists:taxes,id',
            'company_logo' => 'nullable|image|max:2048',
        ]);

        $service->updateSettings($validated, $request->file('company_logo'));

        return redirect()->route('admin.settings.company.index')
            ->with('success', 'تم حفظ إعدادات الشركة.');
    }
}
