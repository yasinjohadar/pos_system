<?php

namespace Tests\Feature;

use App\Models\CashVoucher;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Tax;
use App\Models\User;
use App\Services\Settings\CompanySettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AccountingCompletenessTest extends TestCase
{
    use RefreshDatabase;

    protected function grantPermissions(User $user, array $permissions): void
    {
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $user->givePermissionTo($name);
        }
    }

    public function test_company_settings_page_requires_permission(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->actingAs($user)->get(route('admin.settings.company.index'))->assertForbidden();
    }

    public function test_company_settings_can_be_saved(): void
    {
        Permission::firstOrCreate(['name' => 'settings-manage', 'guard_name' => 'web']);
        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo('settings-manage');

        $response = $this->actingAs($user)->put(route('admin.settings.company.update'), [
            'company_name' => 'Test Co',
            'tax_number' => '123456789',
            'default_currency' => 'SAR',
            'invoice_footer' => 'Thank you',
        ]);

        $response->assertRedirect(route('admin.settings.company.index'));
        $settings = app(CompanySettingsService::class)->getSettings();
        $this->assertSame('Test Co', $settings['company_name']);
        $this->assertSame('123456789', $settings['tax_number']);
    }

    public function test_tax_crud(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->grantPermissions($user, ['tax-list', 'tax-create', 'tax-edit', 'tax-delete']);

        $this->actingAs($user)->get(route('admin.taxes.index'))->assertOk();

        $this->actingAs($user)->post(route('admin.taxes.store'), [
            'name' => 'VAT 15%',
            'type' => 'percent',
            'rate' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.taxes.index'));

        $this->assertDatabaseHas('taxes', ['name' => 'VAT 15%', 'rate' => 15]);
    }

    public function test_manual_journal_entry_requires_balance(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->grantPermissions($user, ['journal-entry-create']);

        $a1 = ChartOfAccount::create(['code' => '1001', 'name' => 'Cash Test', 'type' => 'asset', 'is_active' => true, 'level' => 1]);
        $a2 = ChartOfAccount::create(['code' => '2001', 'name' => 'Payable Test', 'type' => 'liability', 'is_active' => true, 'level' => 1]);

        $response = $this->actingAs($user)->post(route('admin.journal-entries.store'), [
            'entry_date' => now()->toDateString(),
            'description' => 'Test entry',
            'lines' => [
                ['account_id' => $a1->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $a2->id, 'debit' => 0, 'credit' => 50],
            ],
        ]);

        $response->assertSessionHasErrors('lines');
    }

    public function test_balanced_manual_journal_entry_is_created(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->grantPermissions($user, ['journal-entry-create']);

        $a1 = ChartOfAccount::create(['code' => '1002', 'name' => 'Cash 2', 'type' => 'asset', 'is_active' => true, 'level' => 1]);
        $a2 = ChartOfAccount::create(['code' => '2002', 'name' => 'Payable 2', 'type' => 'liability', 'is_active' => true, 'level' => 1]);

        $response = $this->actingAs($user)->post(route('admin.journal-entries.store'), [
            'entry_date' => now()->toDateString(),
            'description' => 'Balanced entry',
            'post_now' => 1,
            'lines' => [
                ['account_id' => $a1->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $a2->id, 'debit' => 0, 'credit' => 100],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('journal_entries', [
            'description' => 'Balanced entry',
            'source' => JournalEntry::SOURCE_MANUAL,
            'is_posted' => true,
        ]);
    }
}
