<?php

namespace App\Support;

use App\Models\BelanjaBahan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Angka ringkasan keuangan untuk satu rentang tanggal:
 * omset (transaksi lunas), diskon, dan modal belanja bahan.
 */
class RingkasanPeriode
{
    /**
     * @return array{omset: float, transaksi: int, diskon: float, total_harga: float, modal: float, laba: float}
     */
    public static function hitung(Carbon $mulai, Carbon $selesai): array
    {
        $penjualan = Transaksi::query()
            ->where('status_pembayaran', 'Lunas')
            ->whereBetween('created_at', [$mulai, $selesai]);

        $omset = (float) (clone $penjualan)->sum('total_akhir');
        $modal = (float) BelanjaBahan::query()
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->sum('harga_total');

        return [
            'omset' => $omset,
            'transaksi' => (int) (clone $penjualan)->count(),
            'diskon' => (float) (clone $penjualan)->sum('diskon'),
            'total_harga' => (float) (clone $penjualan)->sum('total_harga'),
            'modal' => $modal,
            'laba' => $omset - $modal,
        ];
    }

    /**
     * Rincian di dalam periode: per hari untuk mingguan, per minggu untuk bulanan.
     *
     * @return Collection<int, array{label: string, mulai: Carbon, selesai: Carbon, omset: float, transaksi: int, modal: float, laba: float}>
     */
    public static function rincian(string $tipe, Carbon $mulai, Carbon $selesai): Collection
    {
        $potongan = match ($tipe) {
            'mingguan' => 'day',
            'bulanan' => 'week',
            default => null,
        };

        if ($potongan === null) {
            return collect();
        }

        $baris = collect();
        $cursor = $mulai->copy();

        while ($cursor->lte($selesai)) {
            $batasAkhir = $potongan === 'day'
                ? $cursor->copy()->endOfDay()
                : $cursor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

            $awal = $cursor->copy();
            $akhir = $batasAkhir->gt($selesai) ? $selesai->copy() : $batasAkhir;

            $ringkasan = self::hitung($awal, $akhir);
            $baris->push([
                'label' => $potongan === 'day'
                    ? $awal->locale('id')->isoFormat('dddd, D MMM')
                    : 'Minggu ke-'.$awal->isoWeek().' ('.$awal->locale('id')->isoFormat('D MMM').' – '.$akhir->locale('id')->isoFormat('D MMM').')',
                'mulai' => $awal,
                'selesai' => $akhir,
                'omset' => $ringkasan['omset'],
                'transaksi' => $ringkasan['transaksi'],
                'modal' => $ringkasan['modal'],
                'laba' => $ringkasan['laba'],
            ]);

            $cursor = $akhir->copy()->addDay()->startOfDay();
        }

        return $baris;
    }
}
