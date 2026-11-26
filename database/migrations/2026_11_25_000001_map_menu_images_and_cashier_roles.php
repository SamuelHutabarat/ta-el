<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $menuImages = [
            'espresso double' => 'uploads/produk/1794565623_espresso_double.jpg',
            'kopi susu tkp (signature)' => 'uploads/produk/1794565765_kopi_susu_tkp_(signature).jpg',
            'cafe latte' => 'uploads/produk/1794564783_cafe_latte.jpg',
            'coffee latte' => 'uploads/produk/1794564783_cafe_latte.jpg',
            'cappuccino' => 'uploads/produk/1794565178_cappuccino.jpg',
            'americano' => 'uploads/produk/1794564621_americano.jpg',
            'americano dingin' => 'uploads/produk/1794564621_americano.jpg',
            'matcha latte premium' => 'uploads/produk/1794565828_matcha_latte_premium.jpg',
            'chocolate signature' => 'uploads/produk/1794565314_chocolate_signature.webp',
            'chocolate' => 'uploads/produk/1794565314_chocolate_signature.webp',
            'croissant butter' => 'uploads/produk/1794565489_croissant_butter.webp',
            'french fries spicy' => 'uploads/produk/1794565695_french_fries_spicy.jpg',
            'red velvet slice' => 'uploads/produk/1794565896_red_velvet_slice.jpg',
            'kopi susu tkp' => 'uploads/produk/1794565765_kopi_susu_tkp_(signature).jpg',
            'tkp coffee' => 'uploads/produk/1794565765_kopi_susu_tkp_(signature).jpg',
            'spanish coffee' => 'uploads/produk/1794568406_spanish_coffee.jpg',
            'oreo cheese latte' => 'uploads/produk/1794568244_oreo_cheese_latte.jpg',
            'regal vanilla latte' => 'uploads/produk/1794568327_regal_vanilla_latte.jpg',
            'long black' => 'uploads/produk/1794568066_long_black.jpg',
            'hazelnut latte' => 'uploads/produk/1794567470_hazelnut_latte.webp',
            'caramello latte' => 'uploads/produk/1794567217_caramello_latte.jpg',
            'choco latte' => 'uploads/produk/1794567285_choco_latte.webp',
            'avocado latte' => 'uploads/produk/1794567108_avocado_latte.jpg',
            'coffee beer' => 'uploads/produk/1794567380_coffee_beer.jfif',
            'lime sparkling coffee' => 'uploads/produk/1794567989_lime_sparkling_coffee.jpg',
            'matcha fusion latte' => 'uploads/produk/1794568120_matcha_fusion_latte.jpeg',
            'ice lychee yakult tea' => 'uploads/produk/1794567529_ice_lychee_yakult_tea.jpg',
            'jasmine tea' => 'uploads/produk/1794567704_jasmine_tea.jpg',
            'strawberry smoothie' => 'uploads/produk/1794568493_strawberry_smoothie.webp',
            'lemon milk tea' => 'uploads/produk/1794567904_lemon_milk_tea.webp',
            'jasmine milk tea' => 'uploads/produk/1794567626_jasmine_milk_tea.webp',
            'thai tea' => 'uploads/produk/1794568556_thai_tea.jpg',
        ];

        foreach ($menuImages as $productName => $imagePath) {
            if (! is_file(public_path($imagePath))) {
                continue;
            }

            DB::table('produk')
                ->whereRaw('LOWER(nama_produk) = ?', [$productName])
                ->update(['gambar' => $imagePath]);
        }

        // No matching roti bakar photo is in the project assets; remove the incorrect café photo.
        DB::table('produk')
            ->whereRaw('LOWER(nama_produk) = ?', ['roti bakar tkp coklat keju'])
            ->update(['gambar' => null]);

        DB::table('users')
            ->whereIn('email', ['owit@tkp.com', 'tantri@tkp.com'])
            ->where('role', 'pelanggan')
            ->update(['role' => 'barista', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Product photo assignments and staff roles are data corrections; do not restore stale values.
    }
};
