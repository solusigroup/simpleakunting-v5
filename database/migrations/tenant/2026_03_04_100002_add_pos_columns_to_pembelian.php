<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->string('sumber', 20)->default('Manual')->after('status_pembayaran');
            $table->unsignedBigInteger('id_pos_session')->nullable()->after('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn(['sumber', 'id_pos_session']);
        });
    }
};
