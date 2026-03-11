@extends('admin.layouts.master')

@section('page-title')
    سندات القبض والصرف
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">سندات القبض والصرف</h5>
            </div>
            @can('cash-voucher-create')
            <div>
                <a href="{{ route('admin.cash-vouchers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> سند جديد
                </a>
            </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select">
                            <option value="">الكل</option>
                            <option value="receipt" {{ request('type') === 'receipt' ? 'selected' : '' }}>قبض</option>
                            <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>صرف</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الخزنة</label>
                        <select name="treasury_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}" {{ request('treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحساب البنكي</label>
                        <select name="bank_account_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($bankAccounts as $ba)
                                <option value="{{ $ba->id }}" {{ request('bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter me-1"></i> بحث</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>الرقم</th>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>الخزنة / البنك</th>
                                <th>الفئة</th>
                                <th class="text-end">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $v)
                                <tr>
                                    <td>{{ $v->voucher_number }}</td>
                                    <td>{{ $v->date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($v->type === 'receipt')
                                            <span class="badge bg-success">قبض</span>
                                        @else
                                            <span class="badge bg-danger">صرف</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->treasury)
                                            {{ $v->treasury->name }} ({{ $v->treasury->type === 'cashbox' ? 'خزنة' : 'بنك' }})
                                        @elseif($v->bankAccount)
                                            {{ $v->bankAccount->name }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $v->category ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($v->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد سندات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($vouchers->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $vouchers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

