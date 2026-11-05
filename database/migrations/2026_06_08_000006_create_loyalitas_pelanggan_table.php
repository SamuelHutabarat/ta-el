<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loyalitas_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
            $table->decimal('total_akumulasi_transaksi', 15, 2)->default(0);
            $table->string('level_member')->default('Bronze'); // Bronze, Silver, Gold
            $table->integer('diskon_khusus')->default(0); // discount percentage, e.g., 5%
            $table->date('tanggal_bergabung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalitas_pelanggan');
    }
};
