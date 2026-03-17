<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'path',
        'type',
        'description',
        'uploaded_by',
    ];

    public const TYPE_DOCUMENT = 'document';
    public const TYPE_IMAGE = 'image';
    public const TYPE_CONTRACT = 'contract';
    public const TYPE_ID_COPY = 'id_copy';

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
