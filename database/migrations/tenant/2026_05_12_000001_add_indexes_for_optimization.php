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
        // 1. Optimasi Jurnal Umum
        Schema::table('jurnal_umum', function (Blueprint $table) {
            // Index untuk pencarian berdasarkan tanggal (sering digunakan di laporan)
            $table->index('tanggal');
            // Index untuk filter cabang & unit usaha
            $table->index('id_cabang');
            $table->index('id_unit_usaha');
            // Unique index untuk no_transaksi jika belum ada (mempercepat pencarian & menjamin integritas)
            $table->index('no_transaksi');
        });

        // 2. Optimasi Jurnal Detail
        Schema::table('jurnal_detail', function (Blueprint $table) {
            // Index id_jurnal sangat krusial untuk join/hasMany
            $table->index('id_jurnal');
            // Index kode_akun untuk filter Buku Besar dan Neraca
            $table->index('kode_akun');
        });

        // 3. Optimasi Penjualan
        Schema::table('penjualan', function (Blueprint $table) {
            $table->index('tanggal_faktur');
            $table->index('id_pelanggan');
            $table->index('id_cabang');
            $table->index('id_unit_usaha');
            $table->index('no_faktur');
        });

        // 4. Optimasi Pembelian
        Schema::table('pembelian', function (Blueprint $table) {
            $table->index('tanggal_faktur');
            $table->index('id_pemasok');
            $table->index('id_cabang');
            $table->index('id_unit_usaha');
            $table->index('no_faktur_pembelian');
        });

        // 5. Optimasi Simpan Pinjam
        Schema::table('simpanan', function (Blueprint $table) {
            $table->index(['id_anggota', 'tanggal']);
            $table->index('id_cabang');
            $table->index('id_unit_usaha');
        });

        Schema::table('pinjaman', function (Blueprint $table) {
            $table->index(['id_anggota', 'tanggal_pengajuan']);
            $table->index('id_cabang');
            $table->index('id_unit_usaha');
        });

        // 6. Optimasi Persediaan
        Schema::table('master_persediaan', function (Blueprint $table) {
            $table->index('id_cabang');
            $table->index('id_unit_usaha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['id_cabang']);
            $table->dropIndex(['id_unit_usaha']);
            $table->dropIndex(['no_transaksi']);
        });

        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->dropIndex(['id_jurnal']);
            $table->dropIndex(['kode_akun']);
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropIndex(['tanggal_faktur']);
            $table->dropIndex(['id_pelanggan']);
            $table->dropIndex(['id_cabang']);
            $table->dropIndex(['id_unit_usaha']);
            $table->dropIndex(['no_faktur']);
        });

        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropIndex(['tanggal_faktur']);
            $table->dropIndex(['id_pemasok']);
            $table->dropIndex(['id_cabang']);
            $table->dropIndex(['id_unit_usaha']);
            $table->dropIndex(['no_faktur_pembelian']);
        });

        Schema::table('simpanan', function (Blueprint $table) {
            $table->dropIndex(['id_anggota', 'tanggal']);
            $table->dropIndex(['id_cabang']);
            $table->dropIndex(['id_unit_usaha']);
        });

        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropIndex(['id_anggota', 'tanggal_pengajuan']);
            $table->dropIndex(['id_cabang']);
            $table->dropIndex(['id_unit_usaha']);
        });

        Schema::table('master_persediaan', function (Blueprint $table) {
            $table->dropIndex(['id_cabang']);
            $table->dropIndex(['id_unit_usaha']);
        });
    }
};
