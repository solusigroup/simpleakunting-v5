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
        // 1. Retur Penjualan
        Schema::create('retur_penjualan', function (Blueprint $table) {
            $table->id('id_retur_penjualan');
            $table->unsignedBigInteger('id_penjualan')->nullable();
            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_jurnal')->nullable();
            $table->string('no_retur')->unique();
            $table->date('tanggal');
            $table->decimal('total_retur', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('retur_penjualan_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_retur_penjualan');
            $table->unsignedBigInteger('id_barang');
            $table->decimal('kuantitas', 10, 2);
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // 2. Retur Pembelian
        Schema::create('retur_pembelian', function (Blueprint $table) {
            $table->id('id_retur_pembelian');
            $table->unsignedBigInteger('id_pembelian')->nullable();
            $table->unsignedBigInteger('id_pemasok');
            $table->unsignedBigInteger('id_jurnal')->nullable();
            $table->string('no_retur')->unique();
            $table->date('tanggal');
            $table->decimal('total_retur', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('retur_pembelian_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_retur_pembelian');
            $table->unsignedBigInteger('id_barang');
            $table->decimal('kuantitas', 10, 2);
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_pembelian_detail');
        Schema::dropIfExists('retur_pembelian');
        Schema::dropIfExists('retur_penjualan_detail');
        Schema::dropIfExists('retur_penjualan');
    }
};
