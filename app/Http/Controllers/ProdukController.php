<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $produk = Produk::orderBy('id', 'desc')->get();
        return view('owner.produk.index', compact('produk'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('owner.produk.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|in:Kopi,Non-Kopi,Makanan,Cemilan',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp,jfif|max:2048',
        ]);

        $input = $request->only(['nama_produk', 'kategori', 'harga', 'stok', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $name = Str::slug($input['nama_produk']) . '_' . Str::uuid() . '.' . strtolower($image->getClientOriginalExtension());
            
            // Move directly to public/uploads/produk
            $destinationPath = public_path('uploads/produk');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            $image->move($destinationPath, $name);
            $input['gambar'] = 'uploads/produk/' . $name;
        }

        Produk::create($input);

        return redirect()->route($this->productIndexRoute())->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product.
     */
    public function show(Produk $produk)
    {
        return redirect()->route('owner.produk.index');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Produk $produk)
    {
        return view('owner.produk.edit', compact('produk'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|in:Kopi,Non-Kopi,Makanan,Cemilan',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,webp,jfif|max:2048',
        ]);

        $input = $request->only(['nama_produk', 'kategori', 'harga', 'stok', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $name = Str::slug($input['nama_produk']) . '_' . Str::uuid() . '.' . strtolower($image->getClientOriginalExtension());
            
            $destinationPath = public_path('uploads/produk');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            $image->move($destinationPath, $name);

            // Delete old image if it exists
            if ($produk->gambar && File::exists(public_path($produk->gambar))) {
                File::delete(public_path($produk->gambar));
            }

            $input['gambar'] = 'uploads/produk/' . $name;
        }

        $produk->update($input);

        return redirect()->route($this->productIndexRoute())->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Produk $produk)
    {
        // Prevent deleting products that have transaction history to maintain financial and receipt integrity
        if ($produk->detail_transaksi()->exists()) {
            return redirect()->route('owner.produk.index')->with('error', 'Produk "' . $produk->nama_produk . '" tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Silakan ubah stok menjadi 0 untuk menonaktifkan menu.');
        }

        // Delete image file from storage if exists
        if ($produk->gambar && File::exists(public_path($produk->gambar))) {
            File::delete(public_path($produk->gambar));
        }

        $produk->delete();

        return redirect()->route('owner.produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function productIndexRoute(): string
    {
        return auth()->user()?->role === 'kasir' ? 'kasir.produk' : 'owner.produk.index';
    }
}
