<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Services\Storage\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(
        protected AttachmentService $attachmentService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:attachment-list')->only('index');
        $this->middleware('permission:attachment-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Attachment::with(['uploadedBy', 'attachable'])->orderByDesc('created_at');
        if ($request->filled('attachable_type')) {
            $query->where('attachable_type', $request->attachable_type);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $attachments = $query->paginate(20)->withQueryString();
        $types = [Attachment::TYPE_DOCUMENT => 'مستند', Attachment::TYPE_IMAGE => 'صورة', Attachment::TYPE_CONTRACT => 'عقد', Attachment::TYPE_ID_COPY => 'صورة هوية'];
        $attachableTypes = [
            \App\Models\SaleInvoice::class => 'فاتورة بيع',
            \App\Models\PurchaseInvoice::class => 'فاتورة شراء',
            \App\Models\Customer::class => 'عميل',
            \App\Models\Supplier::class => 'مورد',
        ];
        return view('admin.pages.attachments.index', compact('attachments', 'types', 'attachableTypes'));
    }

    /**
     * رفع مرفق لسجل معين (فاتورة، عميل، إلخ).
     * المتوقع: attachable_type (مثل App\Models\SaleInvoice), attachable_id, file, type (اختياري), description (اختياري)
     */
    public function store(Request $request)
    {
        $request->validate([
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
            'file' => 'required|file|max:10240', // 10MB
            'type' => 'nullable|string|in:document,image,contract,id_copy',
            'description' => 'nullable|string|max:500',
        ]);

        $class = $request->attachable_type;
        if (!in_array($class, [\App\Models\SaleInvoice::class, \App\Models\PurchaseInvoice::class, \App\Models\Customer::class, \App\Models\Supplier::class], true)) {
            abort(422, 'نوع المرفق غير مدعوم');
        }
        $model = $class::findOrFail($request->attachable_id);
        $attachment = $this->attachmentService->attach(
            $model,
            $request->file('file'),
            $request->input('type', 'document'),
            $request->input('description')
        );

        if ($request->wantsJson()) {
            return response()->json(['attachment' => $attachment, 'message' => 'تم رفع المرفق بنجاح']);
        }
        return back()->with('success', 'تم رفع المرفق بنجاح');
    }

    public function destroy(Attachment $attachment)
    {
        $this->attachmentService->delete($attachment);
        if (request()->wantsJson()) {
            return response()->json(['message' => 'تم حذف المرفق']);
        }
        return back()->with('success', 'تم حذف المرفق');
    }
}
