<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KritikSaran extends Model
{
    protected $table = 'kritik_saran';

    public const JENIS = ['Kritik', 'Saran'];

    protected $fillable = [
        'pelanggan_id',
        'jenis',
        'isi',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
