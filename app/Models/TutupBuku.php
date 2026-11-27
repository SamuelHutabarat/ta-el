<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TutupBuku extends Model
{
    protected $table = 'tutup_buku';

    protected $fillable = [
        'tipe',
        'periode_mulai',
        'periode_selesai',
        'total_penjualan',
        'total_transaksi',
        'total_diskon',
        'total_modal',
        'laba_kotor',
        'ditutup_oleh',
        'ditutup_at',
        'dibuka_oleh',
        'dibuka_at',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'total_penjualan' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'total_modal' => 'decimal:2',
        'laba_kotor' => 'decimal:2',
        'ditutup_at' => 'datetime',
        'dibuka_at' => 'datetime',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereNull('dibuka_at');
    }

    /** Buku periode yang sedang tertutup dan mencakup tanggal tersebut, jika ada. */
    public static function terkunciPada(Carbon $tanggal): ?self
    {
        return self::aktif()
            ->whereDate('periode_mulai', '<=', $tanggal->toDateString())
            ->whereDate('periode_selesai', '>=', $tanggal->toDateString())
            ->first();
    }

    public function penutup()
    {
        return $this->belongsTo(User::class, 'ditutup_oleh');
    }
}
