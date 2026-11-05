<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'pelanggan_id',
        'user_id',
        'total_harga',
        'diskon',
        'total_akhir',
        'total_bayar',
        'kembalian',
        'metode_pembayaran',
        'status_pembayaran',
        'poin_dapat',
        'poin_digunakan',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detail_transaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }
}
