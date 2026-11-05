<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id',
        'kode_pelanggan',
        'nama',
        'no_telp',
        'alamat',
        'poin',
        'status_member',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'pelanggan_id');
    }

    public function loyalitas_pelanggan()
    {
        return $this->hasOne(LoyalitasPelanggan::class, 'pelanggan_id');
    }
}
