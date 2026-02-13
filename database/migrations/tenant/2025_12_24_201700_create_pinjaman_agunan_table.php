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
        // Tabel Agunan/Jaminan Pinjaman
        Schema::create('pinjaman_agunan', function (Blueprint $table) {
            $table->id('id_agunan');
            $table->unsignedBigInteger('id_pinjaman');
            $table->enum('jenis_agunan', ['sertifikat', 'bpkb', 'sk_kerja', 'deposito', 'lainnya']);
            $table->string('nama_agunan');
            $table->string('no_dokumen')->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('nilai_taksasi', 15, 2)->default(0);
            $table->string('lokasi_penyimpanan')->nullable();
            $table->string('foto_agunan')->nullable();
            $table->enum('status', ['dipegang', 'dikembalikan'])->default('dipegang');
            $table->date('tanggal_terima');
            $table->date('tanggal_kembali')->nullable();
            $table->timestamps();

            $table->foreign('id_pinjaman')->references('id_pinjaman')->on('pinjaman')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_agunan');
    }
};
