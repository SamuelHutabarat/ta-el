<?php

namespace App\Support;

class Telepon
{
    /**
     * Ubah nomor telepon menjadi angka saja dengan awalan 0 (format lokal).
     * Contoh: "+62 812-3456-7890" menjadi "081234567890".
     */
    public static function normalisasi(?string $nomor): ?string
    {
        if ($nomor === null) {
            return null;
        }

        $digit = preg_replace('/\D+/', '', $nomor);

        if ($digit === '') {
            return null;
        }

        if (str_starts_with($digit, '62')) {
            $digit = '0'.substr($digit, 2);
        }

        return $digit;
    }
}
