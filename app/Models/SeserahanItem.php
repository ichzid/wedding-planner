<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeserahanItem extends Model
{
    protected $table = 'seserahan_list';

    protected $fillable = [
        'wedding_id',
        'no',
        'kategori',
        'nama_item',
        'untuk',
        'qty',
        'satuan',
        'harga',
        'status',
    ];

    protected $casts = [
        'harga' => 'integer',
        'qty' => 'integer',
    ];

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    public function getTotalAttribute(): int
    {
        return $this->qty * $this->harga;
    }

    public static function kategoriList(): array
    {
        return [
            'Perlengkapan Ibadah',
            'Pakaian & Sandang',
            'Tas & Sepatu',
            'Kosmetik & Skincare',
            'Perlengkapan Mandi',
            'Perhiasan',
            'Perlengkapan Tidur',
            'Makanan & Buah',
            'Lainnya'
        ];
    }
}
