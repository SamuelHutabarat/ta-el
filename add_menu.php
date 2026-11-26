<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Produk;

// Get existing product names (lowercase for comparison)
$existing = Produk::pluck('nama_produk')->map(fn($n) => strtolower($n))->toArray();

// New menu items to add
$newMenu = [
    // KOPI
    ['nama_produk' => 'Coffee Latte', 'kategori' => 'Kopi', 'harga' => 20000, 'stok' => 100, 'deskripsi' => 'Perpaduan espresso dan susu steamed yang lembut, menghasilkan rasa kopi yang creamy dan smooth.'],
    ['nama_produk' => 'TKP Coffee', 'kategori' => 'Kopi', 'harga' => 20000, 'stok' => 100, 'deskripsi' => 'Racikan kopi signature khas Tempat Kamu Pulang dengan cita rasa unik yang memorable.'],
    ['nama_produk' => 'Cappuccino', 'kategori' => 'Kopi', 'harga' => 20000, 'stok' => 100, 'deskripsi' => 'Espresso klasik dengan foam susu tebal dan lembut, cocok untuk pecinta kopi tradisional.'],
    ['nama_produk' => 'Spanish Coffee', 'kategori' => 'Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Kopi ala Spanyol dengan sentuhan condensed milk yang manis dan creamy.'],
    ['nama_produk' => 'Oreo Cheese Latte', 'kategori' => 'Kopi', 'harga' => 23000, 'stok' => 100, 'deskripsi' => 'Latte dengan topping Oreo crumble dan cream cheese yang gurih manis.'],
    ['nama_produk' => 'Regal Vanilla Latte', 'kategori' => 'Kopi', 'harga' => 23000, 'stok' => 100, 'deskripsi' => 'Latte premium dengan aroma vanilla Regal yang elegan dan mewah.'],
    ['nama_produk' => 'Americano', 'kategori' => 'Kopi', 'harga' => 15000, 'stok' => 100, 'deskripsi' => 'Espresso murni dengan air panas, rasa bold dan clean tanpa campuran susu.'],
    ['nama_produk' => 'Long Black', 'kategori' => 'Kopi', 'harga' => 15000, 'stok' => 100, 'deskripsi' => 'Espresso yang dituang di atas air panas, menjaga crema dan aroma kopi yang kuat.'],
    ['nama_produk' => 'Hazelnut Latte', 'kategori' => 'Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Latte dengan sirup hazelnut yang nutty dan aromatic, favorit para coffee lover.'],
    ['nama_produk' => 'Caramello Latte', 'kategori' => 'Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Latte dengan caramel sauce yang manis dan buttery, cocok untuk sweet tooth.'],
    ['nama_produk' => 'Choco Latte', 'kategori' => 'Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Perpaduan espresso dan cokelat premium yang rich dan indulgent.'],
    ['nama_produk' => 'Avocado Latte', 'kategori' => 'Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Latte unik dengan alpukat segar yang creamy dan menyegarkan.'],
    ['nama_produk' => 'Coffee Beer', 'kategori' => 'Kopi', 'harga' => 18000, 'stok' => 100, 'deskripsi' => 'Kopi segar dengan sensasi sparkling ala beer, non-alkohol dan refreshing.'],
    ['nama_produk' => 'Lime Sparkling Coffee', 'kategori' => 'Kopi', 'harga' => 23000, 'stok' => 100, 'deskripsi' => 'Kopi dengan perasan lime dan soda sparkling, segar dan unik untuk dicoba.'],

    // NON-KOPI
    ['nama_produk' => 'Matcha Fusion Latte', 'kategori' => 'Non-Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Matcha Jepang premium yang di-fusion dengan susu segar, creamy dan earthy.'],
    ['nama_produk' => 'Ice Lychee Yakult Tea', 'kategori' => 'Non-Kopi', 'harga' => 20000, 'stok' => 100, 'deskripsi' => 'Teh segar dengan lychee dan Yakult, manis asam yang menyegarkan.'],
    ['nama_produk' => 'Jasmine Tea', 'kategori' => 'Non-Kopi', 'harga' => 15000, 'stok' => 100, 'deskripsi' => 'Teh melati klasik yang harum dan menenangkan, disajikan hangat atau dingin.'],
    ['nama_produk' => 'Strawberry Smoothie', 'kategori' => 'Non-Kopi', 'harga' => 22000, 'stok' => 100, 'deskripsi' => 'Smoothie strawberry segar yang fruity dan creamy, cocok untuk hari panas.'],
    ['nama_produk' => 'Chocolate', 'kategori' => 'Non-Kopi', 'harga' => 20000, 'stok' => 100, 'deskripsi' => 'Cokelat klasik yang rich dan creamy, minuman favorit sepanjang masa.'],
    ['nama_produk' => 'Lemon Milk Tea', 'kategori' => 'Non-Kopi', 'harga' => 18000, 'stok' => 100, 'deskripsi' => 'Milk tea dengan perasan lemon segar yang memberikan sensasi citrusy dan creamy.'],
    ['nama_produk' => 'Matcha Milk Tea', 'kategori' => 'Non-Kopi', 'harga' => 20000, 'stok' => 100, 'deskripsi' => 'Perpaduan matcha dan milk tea yang earthy dan lembut di lidah.'],
    ['nama_produk' => 'Jasmine Milk Tea', 'kategori' => 'Non-Kopi', 'harga' => 18000, 'stok' => 100, 'deskripsi' => 'Milk tea dengan aroma melati yang harum dan rasa teh yang smooth.'],
    ['nama_produk' => 'Thai Tea', 'kategori' => 'Non-Kopi', 'harga' => 15000, 'stok' => 100, 'deskripsi' => 'Teh Thailand yang manis dan creamy dengan warna oranye khas yang menggoda.'],
];

$added = [];
$skipped = [];

foreach ($newMenu as $item) {
    $nameLower = strtolower($item['nama_produk']);
    
    // Check if already exists (fuzzy match)
    $exists = false;
    foreach ($existing as $ex) {
        if ($ex === $nameLower || str_contains($ex, $nameLower) || str_contains($nameLower, $ex)) {
            $exists = true;
            break;
        }
    }
    
    if ($exists) {
        $skipped[] = $item['nama_produk'];
    } else {
        Produk::create($item);
        $added[] = $item['nama_produk'] . ' (' . $item['kategori'] . ') - Rp ' . number_format($item['harga'], 0, ',', '.');
    }
}

echo "=== PRODUK DITAMBAHKAN ===\n";
foreach ($added as $a) {
    echo "✅ $a\n";
}

echo "\n=== PRODUK DILEWATI (SUDAH ADA) ===\n";
foreach ($skipped as $s) {
    echo "⏭️ $s\n";
}

echo "\n=== TOTAL ===\n";
echo "Ditambahkan: " . count($added) . "\n";
echo "Dilewati: " . count($skipped) . "\n";
echo "Total produk sekarang: " . Produk::count() . "\n";
