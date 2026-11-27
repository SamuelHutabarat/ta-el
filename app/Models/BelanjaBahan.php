<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BelanjaBahan extends Model
{
    protected $table = 'belanja_bahan';

    public const KATEGORI = [
        'Biji Kopi',
        'Susu & Krimer',
        'Sirup & Gula',
        'Bahan Makanan',
        'Kemasan',
        'Lainnya',
    ];

    protected $fillable = [
        'tanggal',
        'nama_bahan',
        'kategori',
        'jumlah',
        'satuan',
        'harga_total',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'harga_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
