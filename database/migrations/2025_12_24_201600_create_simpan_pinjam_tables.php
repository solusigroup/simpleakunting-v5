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
        // Tabel Simpanan
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id('id_simpanan');
            $table->string('no_transaksi')->unique();
            $table->unsignedBigInteger('id_anggota');
            $table->unsignedBigInteger('id_jenis_simpanan');
            $table->unsignedBigInteger('id_jurnal')->nullable();
            $table->date('tanggal');
            $table->enum('jenis_transaksi', ['setor', 'tarik']);
            $table->decimal('jumlah', 15, 2);
            $table->string('akun_kas_bank');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('id_anggota')->references('id_anggota')->on('anggota');
            $table->foreign('id_jenis_simpanan')->references('id_jenis_simpanan')->on('jenis_simpanan');
        });

        // Tabel Header Pinjaman
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id('id_pinjaman');
            $table->string('no_pinjaman')->unique();
            $table->unsignedBigInteger('id_anggota');
            $table->unsignedBigInteger('id_jenis_pinjaman');
            $table->unsignedBigInteger('id_jurnal_pencairan')->nullable();
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_persetujuan')->nullable();
            $table->date('tanggal_pencairan')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('bunga_pertahun', 5, 2);
            $table->enum('metode_bunga', ['flat', 'anuitas', 'efektif']);
            $table->integer('tenor'); // dalam bulan
            $table->decimal('provisi', 15, 2)->default(0);
            $table->decimal('biaya_admin', 15, 2)->default(0);
            $table->decimal('total_bunga', 15, 2)->default(0);
            $table->decimal('total_angsuran', 15, 2)->default(0);
            $table->decimal('sisa_pokok', 15, 2)->default(0);
            $table->decimal('sisa_bunga', 15, 2)->default(0);
            $table->enum('status', [
                'draft', 'pending_approval', 'approved', 'rejected', 
                'disbursed', 'active', 'lunas', 'macet'
            ])->default('draft');
            $table->enum('kolektibilitas', ['1', '2', '3', '4', '5'])->default('1');
            $table->string('akun_kas_bank')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('id_anggota')->references('id_anggota')->on('anggota');
            $table->foreign('id_jenis_pinjaman')->references('id_jenis_pinjaman')->on('jenis_pinjaman');
        });

        // Tabel Jadwal Angsuran
        Schema::create('pinjaman_jadwal', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->unsignedBigInteger('id_pinjaman');
            $table->integer('angsuran_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('pokok', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->decimal('total_angsuran', 15, 2);
            $table->decimal('sisa_pokok_setelah', 15, 2);
            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas'])->default('belum_bayar');
            $table->timestamps();

            $table->foreign('id_pinjaman')->references('id_pinjaman')->on('pinjaman')->onDelete('cascade');
        });

        // Tabel Realisasi Angsuran
        Schema::create('pinjaman_angsuran', function (Blueprint $table) {
            $table->id('id_angsuran');
            $table->string('no_transaksi')->unique();
            $table->unsignedBigInteger('id_pinjaman');
            $table->unsignedBigInteger('id_jadwal')->nullable();
            $table->unsignedBigInteger('id_jurnal')->nullable();
            $table->date('tanggal_bayar');
            $table->decimal('pokok_dibayar', 15, 2);
            $table->decimal('bunga_dibayar', 15, 2);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2);
            $table->string('akun_kas_bank');
            $table->enum('jenis', ['angsuran', 'pelunasan']);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('id_pinjaman')->references('id_pinjaman')->on('pinjaman')->onDelete('cascade');
        });

        // Tabel Approval Config
        Schema::create('approval_config', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // pinjaman, pencairan, dll
            $table->integer('level');
            $table->string('role'); // checker, approver, manager
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Approval History
        Schema::create('approval_history', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->unsignedBigInteger('reference_id');
            $table->integer('level');
            $table->unsignedBigInteger('user_id');
            $table->enum('action', ['submit', 'approve', 'reject', 'return']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_history');
        Schema::dropIfExists('approval_config');
        Schema::dropIfExists('pinjaman_angsuran');
        Schema::dropIfExists('pinjaman_jadwal');
        Schema::dropIfExists('pinjaman');
        Schema::dropIfExists('simpanan');
    }
};
