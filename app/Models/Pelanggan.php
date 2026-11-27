<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    /** Diskon otomatis (persen) untuk tiap level member. */
    public const DISKON = ['Gold' => 10, 'Silver' => 5, 'Bronze' => 0];

    /** Total belanja minimum (Rp) untuk naik ke level tersebut. */
    public const AMBANG_TIER = ['Gold' => 1000000, 'Silver' => 500000];

    protected $fillable = [
        'user_id',
        'kode_pelanggan',
        'nama',
        'no_telp',
        'alamat',
        'poin',
        'status_member',
        'total_belanja',
        'tanggal_lahir',
        'tanggal_bergabung',
    ];

    protected $casts = [
        'poin' => 'integer',
        'total_belanja' => 'decimal:2',
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'pelanggan_id');
    }

    public function kritikSaran()
    {
        return $this->hasMany(KritikSaran::class, 'pelanggan_id');
    }

    /**
     * Hanya pelanggan/member: profil tanpa akun (daftar offline) atau akun dengan role pelanggan.
     * Akun kasir dan owner tidak pernah masuk daftar member.
     */
    public function scopeMember(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('user_id')
                ->orWhereHas('user', fn (Builder $u) => $u->where('role', 'pelanggan'));
        });
    }

    public static function kodeBerikutnya(): string
    {
        $terakhir = self::orderBy('id', 'desc')->first();
        $nomor = $terakhir ? ((int) substr($terakhir->kode_pelanggan, 4)) + 1 : 1;

        return 'PLG-'.str_pad((string) $nomor, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Buat profil member untuk akun pelanggan. Jika ada profil offline (tanpa akun)
     * dengan nomor telepon yang sama, profil itu ditautkan ke akun agar tidak ganda.
     */
    public static function buatUntukAkun(User $user): self
    {
        $profil = $user->no_telp
            ? self::whereNull('user_id')->where('no_telp', $user->no_telp)->first()
            : null;

        if ($profil) {
            $profil->update(['user_id' => $user->id, 'nama' => $user->name]);

            return $profil;
        }

        return self::create([
            'user_id' => $user->id,
            'kode_pelanggan' => self::kodeBerikutnya(),
            'nama' => $user->name,
            'no_telp' => $user->no_telp ?? '-',
            'poin' => 0,
            'status_member' => 'Bronze',
            'total_belanja' => 0,
            'tanggal_bergabung' => Carbon::today()->toDateString(),
        ]);
    }

    public function diskonPersen(): int
    {
        return self::DISKON[$this->status_member] ?? 0;
    }

    public static function tierUntuk(float $totalBelanja): string
    {
        if ($totalBelanja >= self::AMBANG_TIER['Gold']) {
            return 'Gold';
        }

        if ($totalBelanja >= self::AMBANG_TIER['Silver']) {
            return 'Silver';
        }

        return 'Bronze';
    }

    /**
     * Hari penting pelanggan setia (Gold & Silver) dalam rentang hari ke depan:
     * ulang tahun (tanggal lahir) dan ulang tahun keanggotaan (tanggal bergabung).
     *
     * @return Collection<int, array{pelanggan: Pelanggan, jenis: string, tanggal: Carbon, sisa_hari: int}>
     */
    public static function hariPenting(Carbon $hariIni, int $dalamHari = 30): Collection
    {
        $hariIni = $hariIni->copy()->startOfDay();
        $batas = $hariIni->copy()->addDays($dalamHari);
        $hasil = collect();

        self::member()
            ->whereIn('status_member', ['Gold', 'Silver'])
            ->get()
            ->each(function (Pelanggan $p) use ($hariIni, $batas, $hasil) {
                if ($p->tanggal_lahir) {
                    $berikutnya = self::tanggalBerikutnya($p->tanggal_lahir, $hariIni);
                    if ($berikutnya->lte($batas)) {
                        $hasil->push([
                            'pelanggan' => $p,
                            'jenis' => 'Ulang tahun',
                            'tanggal' => $berikutnya,
                            'sisa_hari' => (int) $hariIni->diffInDays($berikutnya),
                        ]);
                    }
                }

                if ($p->tanggal_bergabung) {
                    $berikutnya = self::tanggalBerikutnya($p->tanggal_bergabung, $hariIni);
                    $tahun = $berikutnya->year - $p->tanggal_bergabung->year;
                    if ($tahun >= 1 && $berikutnya->lte($batas)) {
                        $hasil->push([
                            'pelanggan' => $p,
                            'jenis' => 'Anniversary ke-'.$tahun.' jadi member',
                            'tanggal' => $berikutnya,
                            'sisa_hari' => (int) $hariIni->diffInDays($berikutnya),
                        ]);
                    }
                }
            });

        return $hasil->sortBy('sisa_hari')->values();
    }

    private static function tanggalBerikutnya(Carbon $tanggal, Carbon $dari): Carbon
    {
        $berikutnya = Carbon::create($dari->year, $tanggal->month, $tanggal->day)->startOfDay();

        if ($berikutnya->lt($dari)) {
            $berikutnya->addYear();
        }

        return $berikutnya;
    }
}
