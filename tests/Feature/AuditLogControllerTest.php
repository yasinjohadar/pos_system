<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_audit_logs(): void
    {
        $this->get(route('admin.audit-logs.index'))->assertRedirect();
    }

    public function test_authorized_user_can_load_audit_logs_ajax(): void
    {
        Permission::create(['name' => 'manage_audit_logs', 'guard_name' => 'web']);

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo('manage_audit_logs');

        AuditLog::create([
            'user_id' => $user->id,
            'model_type' => \App\Models\SaleInvoice::class,
            'model_id' => 10,
            'action' => AuditLog::ACTION_CREATE,
            'new_values' => ['number' => 'SI-TEST', 'total' => 100],
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.audit-logs.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['tbody', 'pagination']);

        $this->assertStringContainsString('SI-TEST', $response->json('tbody'));
    }

    public function test_show_returns_detail_json(): void
    {
        Permission::create(['name' => 'manage_audit_logs', 'guard_name' => 'web']);

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo('manage_audit_logs');

        $log = AuditLog::create([
            'user_id' => $user->id,
            'model_type' => \App\Models\PurchaseInvoice::class,
            'model_id' => 16,
            'action' => AuditLog::ACTION_CONFIRM,
            'old_values' => ['number' => 'PI-016', 'status' => 'draft'],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->getJson(route('admin.audit-logs.show', $log));

        $response->assertOk()
            ->assertJsonPath('action', AuditLog::ACTION_CONFIRM)
            ->assertJsonPath('model_id', 16)
            ->assertJsonPath('ip_address', '127.0.0.1');
    }
}
