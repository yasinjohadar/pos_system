@php
    $presenter = $presenter ?? app(\App\Support\AuditLogPresenter::class);
@endphp

@forelse ($logs as $log)
    @php
        $changes = $presenter->changes($log);
        $entityUrl = $presenter->entityUrl($log);
    @endphp
    <tr class="audit-row" data-audit-id="{{ $log->id }}">
        <td class="audit-date-cell">{{ $log->created_at->format('Y-m-d H:i') }}</td>
        <td>
            @if ($presenter->isSystemActor($log))
                <span class="audit-actor audit-actor--system">{{ $presenter->actorLabel($log) }}</span>
            @else
                <span class="audit-actor">{{ $presenter->actorLabel($log) }}</span>
            @endif
        </td>
        <td>
            <div class="audit-operation-cell">
                <span class="audit-action-badge {{ $presenter->actionBadgeClass($log->action) }}">
                    {{ $presenter->actionLabel($log->action) }}
                </span>
                <span class="audit-model-label">{{ $presenter->modelLabel($log->model_type) }}</span>
                <span class="audit-model-id">#{{ $log->model_id }}</span>
            </div>
        </td>
        <td class="audit-summary-cell">{{ $presenter->summary($log) }}</td>
        <td class="text-center">
            <div class="audit-row-actions">
                @if ($changes !== [])
                    <button type="button" class="audit-action-btn audit-action-btn--expand"
                        data-expand-target="audit-expand-{{ $log->id }}"
                        title="عرض التغييرات">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                @endif
                <button type="button" class="audit-action-btn audit-action-btn--detail"
                    data-audit-detail="{{ $log->id }}"
                    data-detail-url="{{ route('admin.audit-logs.show', $log) }}"
                    title="عرض التفاصيل">
                    <i class="fas fa-eye"></i>
                </button>
                @if ($entityUrl)
                    <a href="{{ $entityUrl }}" class="audit-action-btn audit-action-btn--link" title="عرض السجل">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
    @if ($changes !== [])
        <tr class="audit-expand-row" id="audit-expand-{{ $log->id }}" hidden>
            <td colspan="5">
                <div class="audit-expand-panel">
                    <h6 class="audit-expand-title"><i class="fas fa-exchange-alt"></i> الحقول المتغيرة</h6>
                    <table class="audit-diff-table">
                        <thead>
                            <tr>
                                <th>الحقل</th>
                                <th>قبل</th>
                                <th>بعد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changes as $change)
                                <tr>
                                    <td>{{ $change['label'] }}</td>
                                    <td>{{ $change['old'] }}</td>
                                    <td>{{ $change['new'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="5" class="text-center py-4">لا توجد سجلات.</td>
    </tr>
@endforelse
