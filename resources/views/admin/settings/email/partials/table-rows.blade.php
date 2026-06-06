@forelse ($settings as $setting)
    <tr class="{{ $setting->is_active ? 'email-row--active' : '' }}">
        <td>
            <div class="users-user-cell">
                <div class="users-avatar email-provider-icon email-provider-icon--{{ $setting->provider }}">
                    @if ($setting->provider === 'gmail')
                        <i class="fab fa-google"></i>
                    @elseif ($setting->provider === 'outlook')
                        <i class="fab fa-microsoft"></i>
                    @else
                        <i class="fas fa-cog"></i>
                    @endif
                </div>
                <span class="users-user-name">{{ $providers[$setting->provider]['name'] ?? 'مخصص' }}</span>
            </div>
        </td>
        <td><code class="email-host-code">{{ $setting->mail_host }}</code></td>
        <td><span class="users-badge users-badge--role">{{ $setting->mail_port }}</span></td>
        <td>
            <div class="users-email-cell">
                <span class="users-email-link">{{ $setting->mail_from_address }}</span>
            </div>
        </td>
        <td><span class="users-badge users-badge--role">{{ strtoupper($setting->mail_encryption) }}</span></td>
        <td>
            @if ($setting->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            @if ($setting->test_results)
                @if ($setting->test_results['status'] === 'success')
                    <span class="users-badge users-badge--active"><i class="fas fa-check"></i> نجح</span>
                @else
                    <span class="users-badge users-badge--inactive"><i class="fas fa-times"></i> فشل</span>
                @endif
                @if ($setting->last_tested_at)
                    <div class="users-muted-text email-tested-at">{{ $setting->last_tested_at->diffForHumans() }}</div>
                @endif
            @else
                <span class="users-muted-text">لم يُختبر</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                <button type="button" class="users-action-btn users-action-btn--view"
                    title="اختبار"
                    onclick="openEmailTestModal({{ $setting->id }}, @json($setting->mail_from_address))">
                    <i class="fas fa-vial"></i>
                </button>

                @if (!$setting->is_active)
                    <form action="{{ route('admin.settings.email.activate', $setting->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="users-action-btn"
                            title="تفعيل"
                            onclick="return confirm('هل تريد تفعيل هذه الإعدادات؟')">
                            <i class="fas fa-check-double"></i>
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.settings.email.edit', $setting->id) }}"
                    class="users-action-btn users-action-btn--edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>

                @if (!$setting->is_active)
                    <form action="{{ route('admin.settings.email.destroy', $setting->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="users-action-btn users-action-btn--delete"
                            title="حذف"
                            onclick="return confirm('هل أنت متأكد من الحذف؟')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-5">
            <div class="email-empty-state">
                <i class="fas fa-envelope-open-text"></i>
                <p>لا توجد إعدادات بريد إلكتروني</p>
                <a href="{{ route('admin.settings.email.create') }}" class="users-btn-create">
                    <i class="fas fa-plus"></i>
                    إضافة إعدادات
                </a>
            </div>
        </td>
    </tr>
@endforelse
