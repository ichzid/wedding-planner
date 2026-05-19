<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeddingChecklist extends Model
{
    protected $table = 'wedding_checklist';

    protected $fillable = [
        'wedding_id',
        'no',
        'bulan_range',
        'persiapan',
        'detail',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    public static function bulanRangeList(): array
    {
        return [
            'H-12 s/d 11 Bulan',
            'H-10 s/d 9 Bulan',
            'H-8 s/d 7 Bulan',
            'H-6 s/d 5 Bulan',
            'H-4 s/d 3 Bulan',
            'H-2 s/d 1 Bulan',
            'H-7 s/d 1 Minggu',
            'Hari-H',
        ];
    }
}
