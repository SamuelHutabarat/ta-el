<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Rentang tanggal untuk laporan: harian, mingguan (Senin–Minggu), atau bulanan.
 */
class Periode
{
    public const TIPE = [
        'harian' => 'Harian',
        'mingguan' => 'Mingguan',
        'bulanan' => 'Bulanan',
    ];

    /**
     * @return array{mulai: Carbon, selesai: Carbon}
     */
    public static function dari(string $tipe, Carbon $acuan): array
    {
        return match ($tipe) {
            'mingguan' => [
                'mulai' => $acuan->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                'selesai' => $acuan->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
            ],
            'bulanan' => [
                'mulai' => $acuan->copy()->startOfMonth()->startOfDay(),
                'selesai' => $acuan->copy()->endOfMonth()->endOfDay(),
            ],
            default => [
                'mulai' => $acuan->copy()->startOfDay(),
                'selesai' => $acuan->copy()->endOfDay(),
            ],
        };
    }

    /**
     * Baca ?periode=harian|mingguan|bulanan&tanggal=YYYY-MM-DD dari request.
     *
     * @return array{tipe: string, acuan: Carbon, mulai: Carbon, selesai: Carbon, label: string, sebelumnya: string, berikutnya: string}
     */
    public static function dariRequest(Request $request, string $defaultTipe = 'harian'): array
    {
        $tipe = array_key_exists((string) $request->query('periode'), self::TIPE)
            ? (string) $request->query('periode')
            : $defaultTipe;

        try {
            $acuan = $request->filled('tanggal')
                ? Carbon::parse((string) $request->query('tanggal'))->startOfDay()
                : Carbon::today();
        } catch (\Throwable) {
            $acuan = Carbon::today();
        }

        $rentang = self::dari($tipe, $acuan);
        [$unit] = match ($tipe) {
            'mingguan' => ['weeks'],
            'bulanan' => ['months'],
            default => ['days'],
        };

        return [
            'tipe' => $tipe,
            'acuan' => $acuan,
            'mulai' => $rentang['mulai'],
            'selesai' => $rentang['selesai'],
            'label' => self::label($tipe, $rentang['mulai'], $rentang['selesai']),
            'sebelumnya' => $acuan->copy()->sub($unit, 1)->toDateString(),
            'berikutnya' => $acuan->copy()->add($unit, 1)->toDateString(),
        ];
    }

    public static function label(string $tipe, Carbon $mulai, Carbon $selesai): string
    {
        return match ($tipe) {
            'mingguan' => 'Minggu ke-'.$mulai->isoWeek().' · '
                .$mulai->locale('id')->isoFormat('D MMM Y').' – '.$selesai->locale('id')->isoFormat('D MMM Y'),
            'bulanan' => $mulai->locale('id')->isoFormat('MMMM Y'),
            default => $mulai->locale('id')->isoFormat('dddd, D MMMM Y'),
        };
    }
}
