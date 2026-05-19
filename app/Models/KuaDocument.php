<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KuaDocument extends Model
{
    protected $table = 'kua_documents';

    protected $fillable = [
        'wedding_id',
        'no',
        'nama_dokumen',
        'cpw_status',
        'cpp_status',
        'biaya',
        'catatan',
    ];

    protected $casts = [
        'cpw_status' => 'boolean',
        'cpp_status' => 'boolean',
        'biaya' => 'integer',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->cpw_status && $this->cpp_status;
    }
}
