@extends('admin.layouts.master')

@section('page-title')
    سجل التدقيق
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">سجل التدقيق</h5>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">المستخدم</label>
                        <select name="user_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">النموذج</label>
                        <select name="model_type" class="form-select">
                            <option value="">الكل</option>
                            @foreach($modelTypes as $t)
                                <option value="{{ $t }}" {{ request('model_type') == $t ? 'selected' : '' }}>{{ class_basename($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الإجراء</label>
                        <select name="action" class="form-select">
                            <option value="">الكل</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> بحث</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>المستخدم</th>
                                <th>النموذج</th>
                                <th>المعرف</th>
                                <th>الإجراء</th>
                                <th>الوصف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user->name ?? '—' }}</td>
                                    <td>{{ class_basename($log->model_type) }}</td>
                                    <td>{{ $log->model_id }}</td>
                                    <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                    <td class="small">
                                        @if($log->new_values)
                                            {{ \Illuminate\Support\Str::limit(json_encode($log->new_values), 60) }}
                                        @elseif($log->old_values)
                                            {{ \Illuminate\Support\Str::limit(json_encode($log->old_values), 60) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">لا توجد سجلات.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
