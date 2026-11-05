<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promosi extends Model
{
    use HasFactory;

    protected $table = 'promosi';

    protected $fillable = [
        'nama_promo',
        'kode_promo',
        'deskripsi',
        'diskon_persen',
        'diskon_nominal',
        'minimal_transaksi',
        'poin_dibutuhkan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status' => 'boolean',
    ];
}
