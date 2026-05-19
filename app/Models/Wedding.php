<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Wedding extends Model
{
    protected $fillable = [
        'user_id',
        'nama_cpw',
        'nama_cpp',
        'tanggal_nikah',
        'lokasi_akad',
        'lokasi_resepsi',
    ];

    protected $casts = [
        'tanggal_nikah' => 'date',
    ];

    public function budgets(): HasMany
    {
        return $this->hasMany(WeddingBudget::class)->orderBy('no');
    }

    public function seserahanList(): HasMany
    {
        return $this->hasMany(SeserahanItem::class, 'wedding_id')->orderBy('no');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(WeddingChecklist::class)->orderBy('no');
    }

    public function kuaDocuments(): HasMany
    {
        return $this->hasMany(KuaDocument::class)->orderBy('no');
    }

    public function getCountdownAttribute(): int
    {
        return max(0, now()->startOfDay()->diffInDays($this->tanggal_nikah, false));
    }

    public function getNamaPasanganAttribute(): string
    {
        return $this->nama_cpw . ' & ' . $this->nama_cpp;
    }
}
