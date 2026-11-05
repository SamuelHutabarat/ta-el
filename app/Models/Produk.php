<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'kategori',
        'harga',
        'deskripsi',
        'stok',
        'gambar',
    ];

    public function detail_transaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'produk_id');
    }
}
