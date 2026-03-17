<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:journal-entry-list')->only('index');
        $this->middleware('permission:journal-entry-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = JournalEntry::with(['createdBy', 'lines.account'])->orderByDesc('entry_date')->orderByDesc('id');
        if ($request->filled('from')) {
            $query->whereDate('entry_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('entry_date', '<=', $request->to);
        }
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }
        $entries = $query->paginate(15)->withQueryString();
        return view('admin.pages.journal-entries.index', compact('entries'));
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account', 'createdBy', 'reference']);
        return view('admin.pages.journal-entries.show', compact('journalEntry'));
    }
}
