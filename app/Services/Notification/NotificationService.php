<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * إرسال تنبيه لمستخدم (يُخزّن في جدول notifications).
     */
    public function notify(User $user, string $type, array $data): void
    {
        $user->notifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => $type,
            'data' => $data,
        ]);
    }

    /**
     * تنبيه: منتجات تحت حد إعادة الطلب.
     */
    public function notifyStockReorderAlert(User $user, int $count): void
    {
        $this->notify($user, 'stock_reorder_alert', [
            'message' => "هناك $count منتج تحت حد إعادة الطلب",
            'count' => $count,
            'url' => route('admin.reports.inventory.reorder'),
        ]);
    }

    /**
     * تنبيه: شيكات قريبة من الاستحقاق.
     */
    public function notifyDueChecks(User $user, int $count): void
    {
        $this->notify($user, 'due_checks', [
            'message' => "هناك $count شيك قريب من تاريخ الاستحقاق",
            'count' => $count,
            'url' => route('admin.checks.index'),
        ]);
    }
}
