<?php

namespace App\Http\Controllers;

use App\Models\KritikSaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KritikSaranController extends Controller
{
    /**
     * Owner: daftar kritik & saran dari seluruh pelanggan.
     */
    public function ownerIndex()
    {
        $daftar = KritikSaran::with('pelanggan')->orderBy('id', 'desc')->get();

        return view('owner.kritik-saran.index', compact('daftar'));
    }

    /**
     * Pelanggan: form kritik & saran beserta riwayat kiriman sendiri.
     */
    public function pelangganIndex()
    {
        $pelanggan = $this->pelangganAkun();
        $daftar = KritikSaran::where('pelanggan_id', $pelanggan->id)->orderBy('id', 'desc')->get();

        return view('pelanggan.kritik-saran', compact('daftar'));
    }

    /**
     * Pelanggan: kirim kritik atau saran.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis' => ['required', Rule::in(KritikSaran::JENIS)],
            'isi' => 'required|string|min:10|max:1000',
        ]);

        KritikSaran::create([
            'pelanggan_id' => $this->pelangganAkun()->id,
            'jenis' => $data['jenis'],
            'isi' => $data['isi'],
        ]);

        return redirect()->route('pelanggan.kritik-saran.index')
            ->with('success', 'Terima kasih! Kritik dan saran Anda sudah kami terima.');
    }

    private function pelangganAkun(): Pelanggan
    {
        $user = auth()->user();

        return $user->pelanggan ?? Pelanggan::buatUntukAkun($user);
    }
}
