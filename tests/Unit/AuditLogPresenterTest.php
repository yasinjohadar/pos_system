<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Support\AuditLogPresenter;
use Tests\TestCase;

class AuditLogPresenterTest extends TestCase
{
    private AuditLogPresenter $presenter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->presenter = new AuditLogPresenter;
    }

    public function test_action_and_model_labels_are_arabic(): void
    {
        $this->assertSame('إنشاء', $this->presenter->actionLabel(AuditLog::ACTION_CREATE));
        $this->assertSame('فاتورة بيع', $this->presenter->modelLabel(\App\Models\SaleInvoice::class));
    }

    public function test_summary_for_create_invoice(): void
    {
        $log = new AuditLog([
            'model_type' => \App\Models\SaleInvoice::class,
            'model_id' => 1,
            'action' => AuditLog::ACTION_CREATE,
            'new_values' => [
                'number' => 'SI-001',
                'total' => 1500,
                'status' => 'draft',
            ],
        ]);

        $summary = $this->presenter->summary($log);

        $this->assertStringContainsString('أنشأ', $summary);
        $this->assertStringContainsString('فاتورة بيع', $summary);
        $this->assertStringContainsString('SI-001', $summary);
    }

    public function test_summary_for_update_shows_changed_field(): void
    {
        $log = new AuditLog([
            'model_type' => \App\Models\StockMovement::class,
            'model_id' => 5,
            'action' => AuditLog::ACTION_UPDATE,
            'old_values' => ['quantity' => 10],
            'new_values' => ['quantity' => 15],
        ]);

        $summary = $this->presenter->summary($log);

        $this->assertStringContainsString('عدّل', $summary);
        $this->assertStringContainsString('الكمية', $summary);
    }

    public function test_actor_label_for_system(): void
    {
        $log = new AuditLog(['user_id' => null]);

        $this->assertSame('النظام', $this->presenter->actorLabel($log));
        $this->assertTrue($this->presenter->isSystemActor($log));
    }

    public function test_changes_returns_formatted_diff(): void
    {
        $log = new AuditLog([
            'model_type' => \App\Models\SaleInvoice::class,
            'action' => AuditLog::ACTION_UPDATE,
            'old_values' => ['status' => 'draft'],
            'new_values' => ['status' => 'confirmed'],
        ]);

        $changes = $this->presenter->changes($log);

        $this->assertCount(1, $changes);
        $this->assertSame('الحالة', $changes[0]['label']);
        $this->assertSame('مسودة', $changes[0]['old']);
        $this->assertSame('مؤكدة', $changes[0]['new']);
    }
}
