<?php

namespace App\Http\Controllers;

use App\Models\TutupBuku;
use App\Support\Periode;
use App\Support\RingkasanPeriode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TutupBukuController extends Controller
{
    /**
     * Daftar periode yang bisa ditutup buku, dan riwayat penutupan.
     */
    public function index()
    {
        $kandidat = $this->kandidat();
        $riwayat = TutupBuku::with('penutup')->orderBy('id', 'desc')->limit(50)->get();

        return view('owner.tutup-buku.index', compact('kandidat', 'riwayat'));
    }

    /**
     * Tutup buku: simpan ringkasan final periode (omset, diskon, modal, laba).
     * Hanya periode yang sudah berakhir yang bisa ditutup.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe' => ['required', Rule::in(array_keys(Periode::TIPE))],
            'tanggal' => 'required|date',
        ]);

        $rentang = Periode::dari($data['tipe'], Carbon::parse($data['tanggal'])->startOfDay());

        if ($rentang['selesai']->toDateString() >= Carbon::today()->toDateString()) {
            return back()->with('error', 'Periode belum berakhir. Buku hanya bisa ditutup setelah periode selesai.');
        }

        $sudahTutup = TutupBuku::aktif()
            ->where('tipe', Periode::TIPE[$data['tipe']])
            ->whereDate('periode_mulai', $rentang['mulai']->toDateString())
            ->exists();

        if ($sudahTutup) {
            return back()->with('error', 'Periode ini sudah ditutup buku.');
        }

        $ringkasan = RingkasanPeriode::hitung($rentang['mulai'], $rentang['selesai']);

        TutupBuku::create([
            'tipe' => Periode::TIPE[$data['tipe']],
            'periode_mulai' => $rentang['mulai']->toDateString(),
            'periode_selesai' => $rentang['selesai']->toDateString(),
            'total_penjualan' => $ringkasan['omset'],
            'total_transaksi' => $ringkasan['transaksi'],
            'total_diskon' => $ringkasan['diskon'],
            'total_modal' => $ringkasan['modal'],
            'laba_kotor' => $ringkasan['laba'],
            'ditutup_oleh' => auth()->id(),
            'ditutup_at' => now(),
        ]);

        return back()->with('success', 'Buku '.Periode::label($data['tipe'], $rentang['mulai'], $rentang['selesai']).' berhasil ditutup.');
    }

    /**
     * Buka kembali buku yang sudah ditutup (untuk koreksi belanja/laporan).
     */
    public function buka(TutupBuku $tutupBuku)
    {
        if ($tutupBuku->dibuka_at) {
            return back()->with('error', 'Buku periode ini sudah dibuka kembali.');
        }

        $tutupBuku->update([
            'dibuka_oleh' => auth()->id(),
            'dibuka_at' => now(),
        ]);

        return back()->with('success', 'Buku '.Periode::label(strtolower($tutupBuku->tipe), $tutupBuku->periode_mulai, $tutupBuku->periode_selesai).' dibuka kembali.');
    }

    /**
     * Periode yang sudah berakhir: 7 hari terakhir, 4 minggu terakhir, 6 bulan terakhir.
     */
    private function kandidat(): array
    {
        $hariIni = Carbon::today();
        $daftar = [];

        foreach (range(1, 7) as $n) {
            $daftar[] = $this->baris('harian', $hariIni->copy()->subDays($n));
        }

        foreach (range(1, 4) as $n) {
            $daftar[] = $this->baris('mingguan', $hariIni->copy()->startOfWeek(Carbon::MONDAY)->subWeeks($n));
        }

        foreach (range(1, 6) as $n) {
            $daftar[] = $this->baris('bulanan', $hariIni->copy()->startOfMonth()->subMonths($n));
        }

        return $daftar;
    }

    private function baris(string $tipe, Carbon $acuan): array
    {
        $rentang = Periode::dari($tipe, $acuan);
        $aktif = TutupBuku::aktif()
            ->where('tipe', Periode::TIPE[$tipe])
            ->whereDate('periode_mulai', $rentang['mulai']->toDateString())
            ->first();

        return [
            'tipe' => $tipe,
            'label' => Periode::label($tipe, $rentang['mulai'], $rentang['selesai']),
            'mulai' => $rentang['mulai']->toDateString(),
            'selesai' => $rentang['selesai']->toDateString(),
            'tutup' => $aktif,
        ];
    }
}
