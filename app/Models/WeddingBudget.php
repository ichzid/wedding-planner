<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeddingBudget extends Model
{
    protected $table = 'wedding_budget';

    protected $fillable = [
        'wedding_id',
        'no',
        'kategori',
        'item',
        'vendor',
        'estimasi_budget',
        'dp',
        'pelunasan',
        'sumber_dana',
        'status',
        'catatan',
    ];

    protected $casts = [
        'estimasi_budget' => 'integer',
        'dp' => 'integer',
        'pelunasan' => 'integer',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    public function getAktualAttribute(): int
    {
        return $this->dp + $this->pelunasan;
    }

    public function getSisaAttribute(): int
    {
        return $this->estimasi_budget - $this->aktual;
    }

    public static function kategoriList(): array
    {
        return [
            'Venue',
            'Seserahan',
            'Attire & Makeup',
            'Catering',
            'Entertainment',
            'Transport',
            'Dokumentasi',
            'Lainnya',
        ];
    }

    public static function statusList(): array
    {
        return [
            'belum' => 'Belum',
            'dp_terbayar' => 'DP Terbayar',
            'lunas' => 'Lunas',
        ];
    }
}
