<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$produk = \App\Models\Produk::select('id','nama_produk','kategori','harga','stok','gambar')->get();
echo json_encode($produk->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
