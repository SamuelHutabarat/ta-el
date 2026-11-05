<?php

namespace App\Http\Controllers;

use App\Models\Promosi;
use Illuminate\Http\Request;

class PromosiController extends Controller
{
    /**
     * Display a listing of active and inactive promotions.
     */
    public function index()
    {
        $promosi = Promosi::orderBy('id', 'desc')->get();
        return view('owner.promosi.index', compact('promosi'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        return view('owner.promosi.create');
    }

    /**
     * Store a newly created promotion in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_promo' => 'required|string|max:255',
            'kode_promo' => 'required|string|max:50|unique:promosi,kode_promo',
            'deskripsi' => 'nullable|string',
            'diskon_persen' => 'required|integer|min:0|max:100',
            'diskon_nominal' => 'required|numeric|min:0',
            'minimal_transaksi' => 'required|numeric|min:0',
            'poin_dibutuhkan' => 'required|integer|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|boolean',
        ]);

        Promosi::create($request->all());

        return redirect()->route('owner.promosi.index')->with('success', 'Promosi berhasil dibuat.');
    }

    /**
     * Display the specified promotion.
     */
    public function show(Promosi $promosi)
    {
        return redirect()->route('owner.promosi.index');
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(Promosi $promosi)
    {
        return view('owner.promosi.edit', compact('promosi'));
    }

    /**
     * Update the specified promotion in storage.
     */
    public function update(Request $request, Promosi $promosi)
    {
        $request->validate([
            'nama_promo' => 'required|string|max:255',
            'kode_promo' => 'required|string|max:50|unique:promosi,kode_promo,' . $promosi->id,
            'deskripsi' => 'nullable|string',
            'diskon_persen' => 'required|integer|min:0|max:100',
            'diskon_nominal' => 'required|numeric|min:0',
            'minimal_transaksi' => 'required|numeric|min:0',
            'poin_dibutuhkan' => 'required|integer|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|boolean',
        ]);

        $promosi->update($request->all());

        return redirect()->route('owner.promosi.index')->with('success', 'Promosi berhasil diperbarui.');
    }

    /**
     * Remove the specified promotion from storage.
     */
    public function destroy(Promosi $promosi)
    {
        $promosi->delete();
        return redirect()->route('owner.promosi.index')->with('success', 'Promosi berhasil dihapus.');
    }
}
