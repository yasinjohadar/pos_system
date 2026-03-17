<?php

namespace App\Services\Storage;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class AttachmentService
{
    public function attach(Model $model, UploadedFile $file, string $type = 'document', ?string $description = null): Attachment
    {
        $filename = $file->store('attachments/' . $model->getTable() . '/' . $model->getKey(), 'public');
        if ($filename === false) {
            $filename = $file->store('attachments', 'public');
        }
        return Attachment::create([
            'attachable_type' => $model->getMorphClass(),
            'attachable_id' => $model->getKey(),
            'filename' => basename($filename),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $filename,
            'type' => $type,
            'description' => $description,
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function delete(Attachment $attachment): bool
    {
        if (\Storage::disk('public')->exists($attachment->path)) {
            \Storage::disk('public')->delete($attachment->path);
        }
        return $attachment->delete();
    }
}
