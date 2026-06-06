<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Customer;
use App\Models\SaleInvoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        if (Attachment::exists()) {
            $this->command?->info('AttachmentSeeder: مرفقات موجودة — تخطي.');

            return;
        }

        $user = User::query()->first();
        if (! $user) {
            $this->command?->warn('AttachmentSeeder: يتطلب مستخدماً.');

            return;
        }

        $scenarios = [];

        $invoices = SaleInvoice::orderBy('id')->limit(4)->get();
        foreach ($invoices as $i => $invoice) {
            $scenarios[] = [
                'attachable' => $invoice,
                'original_filename' => 'فاتورة-' . $invoice->number . '.pdf',
                'mime_type' => 'application/pdf',
                'type' => Attachment::TYPE_DOCUMENT,
                'description' => 'نسخة PDF للفاتورة',
            ];
        }

        $customers = Customer::orderBy('id')->limit(3)->get();
        foreach ($customers as $i => $customer) {
            $scenarios[] = [
                'attachable' => $customer,
                'original_filename' => 'هوية-' . ($customer->name ?? 'عميل') . '.jpg',
                'mime_type' => 'image/jpeg',
                'type' => $i === 0 ? Attachment::TYPE_ID_COPY : Attachment::TYPE_CONTRACT,
                'description' => $i === 0 ? 'صورة الهوية الوطنية' : 'عقد توريد',
            ];
        }

        if (empty($scenarios)) {
            $this->command?->warn('AttachmentSeeder: لا توجد فواتير أو عملاء لربط المرفقات.');

            return;
        }

        $created = 0;

        foreach ($scenarios as $scenario) {
            $model = $scenario['attachable'];
            $dir = 'attachments/' . $model->getTable() . '/' . $model->getKey();
            $filename = 'seed-' . ($created + 1) . '.' . pathinfo($scenario['original_filename'], PATHINFO_EXTENSION);
            $path = $dir . '/' . $filename;

            $content = "مرفق تجريبي — {$scenario['original_filename']}\nتم الإنشاء بواسطة AttachmentSeeder.";
            Storage::disk('public')->put($path, $content);

            Attachment::create([
                'attachable_type' => $model->getMorphClass(),
                'attachable_id' => $model->getKey(),
                'filename' => $filename,
                'original_filename' => $scenario['original_filename'],
                'mime_type' => $scenario['mime_type'],
                'size' => strlen($content),
                'path' => $path,
                'type' => $scenario['type'],
                'description' => $scenario['description'],
                'uploaded_by' => $user->id,
            ]);

            $created++;
        }

        $this->command?->info("AttachmentSeeder: تم إنشاء {$created} مرفق.");
    }
}
