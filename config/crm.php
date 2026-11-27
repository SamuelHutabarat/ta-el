<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aturan Poin Reward
    |--------------------------------------------------------------------------
    | Setiap kelipatan nilai belanja (Rupiah, setelah diskon) menghasilkan 1 poin.
    | Transaksi datang langsung (Offline) dan pesanan online (Online) dibedakan
    | agar penambahan poin per kanal bisa diatur dan dibandingkan.
    */
    'rupiah_per_poin' => [
        'Offline' => 10000, // datang langsung ke kasir
        'Online' => 20000,  // pesanan online (poin lebih kecil)
    ],

];
