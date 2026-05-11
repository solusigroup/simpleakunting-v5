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
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pelanggan')->nullable()->after('deskripsi');
            $table->unsignedBigInteger('id_pemasok')->nullable()->after('id_pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropColumn(['id_pelanggan', 'id_pemasok']);
        });
    }
};
