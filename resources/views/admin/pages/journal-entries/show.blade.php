@extends('admin.layouts.master')

@section('page-title')
    تفاصيل القيد
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">قيد: {{ $journalEntry->entry_number }}</h4>
            <a href="{{ route('admin.journal-entries.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>التاريخ:</strong> {{ $journalEntry->entry_date->format('Y-m-d') }}</p>
                <p><strong>الوصف:</strong> {{ $journalEntry->description }}</p>
                <p><strong>المرجع:</strong> {{ $journalEntry->reference_type ? class_basename($journalEntry->reference_type) . ' #' . $journalEntry->reference_id : '—' }}</p>
                <p><strong>أنشئ بواسطة:</strong> {{ $journalEntry->createdBy->name ?? '—' }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h6 class="mb-0">تفاصيل القيد</h6></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الحساب</th>
                            <th>الكود</th>
                            <th class="text-end">مدين</th>
                            <th class="text-end">دائن</th>
                            <th>الوصف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalEntry->lines as $line)
                            <tr>
                                <td>{{ $line->account->name ?? '—' }}</td>
                                <td>{{ $line->account->code ?? '—' }}</td>
                                <td class="text-end">{{ (float)$line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
                                <td class="text-end">{{ (float)$line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
                                <td>{{ $line->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
