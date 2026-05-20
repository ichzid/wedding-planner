<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeddingGuest extends Model
{
    protected $table = 'wedding_guests';

    protected $fillable = [
        'wedding_id',
        'no',
        'nama_tamu',
        'pihak',
        'status',
        'catatan',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }
}
