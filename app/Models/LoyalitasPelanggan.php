<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyalitasPelanggan extends Model
{
    use HasFactory;

    protected $table = 'loyalitas_pelanggan';

    protected $fillable = [
        'pelanggan_id',
        'total_akumulasi_transaksi',
        'level_member',
        'diskon_khusus',
        'tanggal_bergabung',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
