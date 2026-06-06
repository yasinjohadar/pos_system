@extends('admin.layouts.master')

@section('page-title')
    إعدادات التخزين
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">إعدادات التخزين</h5>
                    <a href="{{ route('admin.backup-storage.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        إضافة مكان تخزين
                    </a>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">الاسم</th>
                                    <th style="min-width: 140px;">النوع</th>
                                    <th style="min-width: 100px;">الحالة</th>
                                    <th style="min-width: 90px;">الأولوية</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.backup-storage.partials.table-rows', ['configs' => $configs])
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        document.querySelectorAll('.test-storage-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                var url = btn.getAttribute('data-test-url');
                var originalHtml = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                try {
                    var response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    var data = await response.json();

                    if (data.success) {
                        AdminPremium.showToast(data.message, 'success');
                    } else {
                        AdminPremium.showToast(data.message || 'فشل الاختبار', 'error');
                    }
                } catch (error) {
                    AdminPremium.showToast('حدث خطأ أثناء الاختبار', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        });
    </script>
@stop
