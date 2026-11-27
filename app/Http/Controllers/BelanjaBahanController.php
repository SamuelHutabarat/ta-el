<?php

namespace App\Http\Controllers;

use App\Models\BelanjaBahan;
use App\Models\TutupBuku;
use App\Support\Periode;
use App\Support\RingkasanPeriode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BelanjaBahanController extends Controller
{
    /**
     * Daftar belanja bahan pada periode terpilih + rekap modal per minggu & per bulan.
     */
    public function index(Request $request)
    {
        $periode = Periode::dariRequest($request, 'bulanan');

        $entri = BelanjaBahan::with('user')
            ->whereBetween('tanggal', [$periode['mulai']->toDateString(), $periode['selesai']->toDateString()])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalPeriode = (float) $entri->sum('harga_total');
        $rekapMinggu = $this->rekap('mingguan', 12);
        $rekapBulan = $this->rekap('bulanan', 12);

        return view('owner.belanja.index', compact('periode', 'entri', 'totalPeriode', 'rekapMinggu', 'rekapBulan'));
    }

    public function create()
    {
        $belanja = new BelanjaBahan([
            'tanggal' => Carbon::today()->toDateString(),
            'kategori' => 'Bahan Makanan',
            'jumlah' => 1,
            'satuan' => 'pcs',
        ]);

        return view('owner.belanja.create', compact('belanja'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($kunci = $this->terkunci($data['tanggal'])) {
            return back()->withInput()->with('error', 'Periode '.$kunci->tipe.' tanggal tersebut sudah ditutup buku. Buka kembali buku periode itu jika ingin menambah belanja.');
        }

        BelanjaBahan::create($data + ['user_id' => auth()->id()]);

        return redirect()->route('owner.belanja-bahan.index', ['tanggal' => $data['tanggal'], 'periode' => 'bulanan'])
            ->with('success', 'Belanja bahan berhasil dicatat.');
    }

    public function edit(BelanjaBahan $belanjaBahan)
    {
        return view('owner.belanja.edit', ['belanja' => $belanjaBahan]);
    }

    public function update(Request $request, BelanjaBahan $belanjaBahan)
    {
        $data = $this->validasi($request);

        if ($kunci = $this->terkunci($belanjaBahan->tanggal->toDateString()) ?? $this->terkunci($data['tanggal'])) {
            return back()->withInput()->with('error', 'Periode '.$kunci->tipe.' sudah ditutup buku. Buka kembali buku periode itu untuk mengubah belanja.');
        }

        $belanjaBahan->update($data);

        return redirect()->route('owner.belanja-bahan.index', ['tanggal' => $data['tanggal'], 'periode' => 'bulanan'])
            ->with('success', 'Belanja bahan berhasil diperbarui.');
    }

    public function destroy(BelanjaBahan $belanjaBahan)
    {
        if ($kunci = $this->terkunci($belanjaBahan->tanggal->toDateString())) {
            return back()->with('error', 'Periode '.$kunci->tipe.' sudah ditutup buku. Belanja ini tidak dapat dihapus.');
        }

        $belanjaBahan->delete();

        return back()->with('success', 'Belanja bahan berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'nama_bahan' => 'required|string|max:120',
            'kategori' => ['required', Rule::in(BelanjaBahan::KATEGORI)],
            'jumlah' => 'required|numeric|min:0.01',
            'satuan' => 'required|string|max:20',
            'harga_total' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);
    }

    /** Buku periode yang menutup tanggal ini (jika ada). */
    private function terkunci(string $tanggal): ?TutupBuku
    {
        return TutupBuku::terkunciPada(Carbon::parse($tanggal));
    }

    /**
     * Rekap modal belanja & omset per minggu/bulan, dari yang terbaru.
     */
    private function rekap(string $tipe, int $jumlah)
    {
        return collect(range(0, $jumlah - 1))->map(function (int $i) use ($tipe) {
            $acuan = $tipe === 'mingguan'
                ? Carbon::today()->subWeeks($i)
                : Carbon::today()->subMonths($i);
            $rentang = Periode::dari($tipe, $acuan);
            $ringkasan = RingkasanPeriode::hitung($rentang['mulai'], $rentang['selesai']);

            return [
                'label' => Periode::label($tipe, $rentang['mulai'], $rentang['selesai']),
                'tipe' => $tipe,
                'mulai' => $rentang['mulai']->toDateString(),
                'omset' => $ringkasan['omset'],
                'modal' => $ringkasan['modal'],
                'laba' => $ringkasan['laba'],
                'transaksi' => $ringkasan['transaksi'],
            ];
        });
    }
}
