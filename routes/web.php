<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PromosiController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// Redirect from homepage to login or dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Post-login redirect router based on roles
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isOwner()) {
        return redirect()->route('owner.dashboard');
    } elseif ($user->isBarista()) {
        return redirect()->route('barista.dashboard');
    } elseif ($user->isPelanggan()) {
        return redirect()->route('pelanggan.dashboard');
    }
    abort(403, 'Role tidak terdefinisi.');
})->middleware(['auth'])->name('dashboard');

// Profile routes (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// 1. OWNER ROUTES (Role: owner)
// ==========================================
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
    
    // CRUD Produk (Menu Kopi)
    Route::resource('produk', ProdukController::class);
    
    // CRUD Pelanggan (CRM Member)
    Route::resource('pelanggan', PelangganController::class);
    
    // CRUD Promosi
    Route::resource('promosi', PromosiController::class);
    
    // CRUD Pengguna (Users/Employees)
    Route::resource('pengguna', OwnerController::class)->except(['dashboard', 'show']);
    
    // Kelola Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
    
    // Laporan Penjualan & Export
    Route::get('/laporan', [OwnerController::class, 'laporan'])->name('laporan.index');
    Route::get('/laporan/export-excel', [OwnerController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/export-pdf', [OwnerController::class, 'exportPdf'])->name('laporan.pdf');
    
    // Monitoring Loyalitas Pelanggan
    Route::get('/loyalitas', [OwnerController::class, 'loyalitas'])->name('loyalitas.index');
});

// ==========================================
// 2. BARISTA ROUTES (Role: owner, barista)
// ==========================================
Route::middleware(['auth', 'role:owner,barista'])->prefix('barista')->name('barista.')->group(function () {
    Route::get('/dashboard', [TransaksiController::class, 'baristaDashboard'])->name('dashboard');
    Route::get('/produk', [TransaksiController::class, 'baristaProduk'])->name('produk');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
    
    // POS / Checkout Transaksi
    Route::get('/transaksi/baru', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi/checkout', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi', [TransaksiController::class, 'baristaHistory'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
});

// ==========================================
// 3. PELANGGAN ROUTES (Role: pelanggan)
// ==========================================
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/dashboard', [PelangganController::class, 'dashboard'])->name('dashboard');
    Route::get('/produk', [PelangganController::class, 'produk'])->name('produk');
    Route::get('/promosi', [PelangganController::class, 'promosi'])->name('promosi');
    Route::get('/transaksi', [PelangganController::class, 'transaksi'])->name('transaksi.index');
    Route::get('/transaksi/{id}', [PelangganController::class, 'transaksiDetail'])->name('transaksi.show');
    Route::post('/promosi/claim', [PelangganController::class, 'claimPromo'])->name('promosi.claim');
});

require __DIR__.'/auth.php';
