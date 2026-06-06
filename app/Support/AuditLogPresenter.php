<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AuditLogPresenter
{
    public function actionLabel(string $action): string
    {
        return config("audit.actions.{$action}", $action);
    }

    public function actionBadgeClass(string $action): string
    {
        return match ($action) {
            AuditLog::ACTION_CREATE => 'audit-action-badge--create',
            AuditLog::ACTION_UPDATE => 'audit-action-badge--update',
            AuditLog::ACTION_DELETE => 'audit-action-badge--delete',
            AuditLog::ACTION_CONFIRM => 'audit-action-badge--confirm',
            AuditLog::ACTION_CANCEL => 'audit-action-badge--cancel',
            default => 'audit-action-badge--default',
        };
    }

    public function modelLabel(string $modelType): string
    {
        $config = config("audit.models.{$modelType}");

        if (is_array($config) && ! empty($config['label'])) {
            return $config['label'];
        }

        return Str::headline(class_basename($modelType));
    }

    public function actorLabel(AuditLog $log): string
    {
        if ($log->relationLoaded('user') && $log->user) {
            return $log->user->name;
        }

        if ($log->user_id) {
            return (string) $log->user_id;
        }

        return config('audit.system_actor_label', 'النظام');
    }

    public function isSystemActor(AuditLog $log): bool
    {
        return $log->user_id === null;
    }

    public function summary(AuditLog $log): string
    {
        $verb = config("audit.action_verbs.{$log->action}", $this->actionLabel($log->action));
        $model = $this->modelLabel($log->model_type);
        $data = $this->primaryData($log);
        $parts = ["{$verb} {$model}"];

        $details = $this->summaryDetails($log->model_type, $data);
        if ($details !== '') {
            $parts[] = $details;
        }

        if ($log->action === AuditLog::ACTION_UPDATE) {
            $changes = $this->changes($log);
            if ($changes !== []) {
                $first = $changes[0];
                if (count($changes) === 1) {
                    $parts[] = "— {$first['label']}: {$first['old']} ← {$first['new']}";
                } else {
                    $parts[] = '— ' . count($changes) . ' حقول متغيرة';
                }
            }
        }

        return implode(' ', $parts);
    }

    /**
     * @return array<int, array{field: string, label: string, old: string, new: string}>
     */
    public function changes(AuditLog $log): array
    {
        $old = $this->filterValues($log->old_values ?? []);
        $new = $this->filterValues($log->new_values ?? []);
        $fields = array_unique(array_merge(array_keys($old), array_keys($new)));
        $changes = [];

        foreach ($fields as $field) {
            $oldRaw = $old[$field] ?? null;
            $newRaw = $new[$field] ?? null;

            if ($this->valuesEqual($oldRaw, $newRaw)) {
                continue;
            }

            $changes[] = [
                'field' => $field,
                'label' => $this->fieldLabel($log->model_type, $field),
                'old' => $this->formatValue($field, $oldRaw),
                'new' => $this->formatValue($field, $newRaw),
            ];
        }

        return $changes;
    }

    public function entityUrl(AuditLog $log): ?string
    {
        $routeName = config("audit.models.{$log->model_type}.route");

        if (! $routeName || ! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName, $log->model_id);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toDetailArray(AuditLog $log): array
    {
        return [
            'id' => $log->id,
            'summary' => $this->summary($log),
            'action' => $log->action,
            'action_label' => $this->actionLabel($log->action),
            'action_badge_class' => $this->actionBadgeClass($log->action),
            'model_type' => $log->model_type,
            'model_label' => $this->modelLabel($log->model_type),
            'model_id' => $log->model_id,
            'actor' => $this->actorLabel($log),
            'is_system' => $this->isSystemActor($log),
            'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'entity_url' => $this->entityUrl($log),
            'changes' => $this->changes($log),
            'old_values' => $this->formatValuesForDisplay($log->model_type, $log->old_values ?? []),
            'new_values' => $this->formatValuesForDisplay($log->model_type, $log->new_values ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function summaryDetails(string $modelType, array $data): string
    {
        $keys = config("audit.models.{$modelType}.summary_keys", ['number', 'total']);
        $chunks = [];

        foreach ($keys as $key) {
            if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
                continue;
            }

            $label = $this->fieldLabel($modelType, $key);
            $value = $this->formatValue($key, $data[$key]);
            $chunks[] = "{$label} {$value}";
        }

        return implode(' — ', $chunks);
    }

    /**
     * @return array<string, mixed>
     */
    private function primaryData(AuditLog $log): array
    {
        return match ($log->action) {
            AuditLog::ACTION_CREATE => $this->filterValues($log->new_values ?? []),
            AuditLog::ACTION_DELETE => $this->filterValues($log->old_values ?? []),
            AuditLog::ACTION_UPDATE => $this->filterValues(array_merge(
                $log->old_values ?? [],
                $log->new_values ?? []
            )),
            default => $this->filterValues($log->old_values ?? $log->new_values ?? []),
        };
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function filterValues(array $values): array
    {
        $hidden = config('audit.hidden_fields', []);

        return array_filter(
            $values,
            fn ($value, $key) => ! in_array($key, $hidden, true) && $value !== null && $value !== '',
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function fieldLabel(string $modelType, string $field): string
    {
        return config("audit.models.{$modelType}.fields.{$field}")
            ?? Str::headline(str_replace('_', ' ', $field));
    }

    private function formatValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        $maps = config('audit.value_maps', []);
        foreach ($maps as $mapKey => $map) {
            if ($field === $mapKey || str_ends_with($field, "_{$mapKey}")) {
                $key = (string) $value;
                if (isset($map[$key])) {
                    return $map[$key];
                }
            }
        }

        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }

        if (is_numeric($value) && in_array($field, ['total', 'amount', 'quantity', 'opening_balance'], true)) {
            return number_format((float) $value, str_contains((string) $value, '.') ? 2 : 0);
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, string>
     */
    private function formatValuesForDisplay(string $modelType, array $values): array
    {
        $formatted = [];

        foreach ($this->filterValues($values) as $field => $value) {
            $formatted[$this->fieldLabel($modelType, $field)] = $this->formatValue($field, $value);
        }

        return $formatted;
    }

    private function valuesEqual(mixed $old, mixed $new): bool
    {
        if ($old == $new) {
            return true;
        }

        return json_encode($old) === json_encode($new);
    }
}
