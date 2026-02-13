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
        // Tabel Anggota Koperasi
        Schema::create('anggota', function (Blueprint $table) {
            $table->id('id_anggota');
            $table->string('no_anggota')->unique();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal_daftar');
            $table->enum('status', ['aktif', 'non_aktif', 'keluar'])->default('aktif');
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();
        });

        // Tabel Jenis Simpanan
        Schema::create('jenis_simpanan', function (Blueprint $table) {
            $table->id('id_jenis_simpanan');
            $table->string('kode_simpanan')->unique();
            $table->string('nama_simpanan');
            $table->enum('tipe', ['pokok', 'wajib', 'sukarela', 'deposito']);
            $table->decimal('bunga_pertahun', 5, 2)->default(0);
            $table->string('akun_simpanan'); // Kewajiban
            $table->string('akun_bunga')->nullable(); // Beban Bunga
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Jenis Pinjaman
        Schema::create('jenis_pinjaman', function (Blueprint $table) {
            $table->id('id_jenis_pinjaman');
            $table->string('kode_pinjaman')->unique();
            $table->string('nama_pinjaman');
            $table->enum('kategori', ['produktif', 'konsumtif', 'darurat']);
            $table->decimal('bunga_pertahun', 5, 2);
            $table->enum('metode_bunga', ['flat', 'anuitas', 'efektif']);
            $table->integer('tenor_max'); // dalam bulan
            $table->decimal('plafon_max', 15, 2);
            $table->decimal('provisi_persen', 5, 2)->default(0);
            $table->decimal('admin_fee', 15, 2)->default(0);
            $table->string('akun_piutang_pinjaman');
            $table->string('akun_pendapatan_bunga');
            $table->string('akun_pendapatan_provisi')->nullable();
            $table->string('akun_pendapatan_admin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pinjaman');
        Schema::dropIfExists('jenis_simpanan');
        Schema::dropIfExists('anggota');
    }
};
