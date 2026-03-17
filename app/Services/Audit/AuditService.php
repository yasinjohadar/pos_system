<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * تسجيل عملية على نموذج في سجل التدقيق.
     *
     * @param  string  $action  create|update|delete|confirm|cancel
     */
    public function log(Model $model, string $action, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * تسجيل إنشاء سجل جديد.
     */
    public function logCreate(Model $model): AuditLog
    {
        return $this->log($model, AuditLog::ACTION_CREATE, null, $model->getAttributes());
    }

    /**
     * تسجيل تحديث مع القيم القديمة والجديدة.
     */
    public function logUpdate(Model $model, array $oldValues, array $newValues): AuditLog
    {
        return $this->log($model, AuditLog::ACTION_UPDATE, $oldValues, $newValues);
    }

    /**
     * تسجيل حذف مع القيم المحذوفة.
     */
    public function logDelete(Model $model): AuditLog
    {
        return $this->log($model, AuditLog::ACTION_DELETE, $model->getAttributes(), null);
    }

    /**
     * تسجيل تأكيد (مثل تأكيد فاتورة).
     */
    public function logConfirm(Model $model, ?array $snapshot = null): AuditLog
    {
        return $this->log($model, AuditLog::ACTION_CONFIRM, $snapshot ?? $model->getAttributes(), null);
    }

    /**
     * تسجيل إلغاء.
     */
    public function logCancel(Model $model, ?array $snapshot = null): AuditLog
    {
        return $this->log($model, AuditLog::ACTION_CANCEL, $snapshot ?? $model->getAttributes(), null);
    }
}
