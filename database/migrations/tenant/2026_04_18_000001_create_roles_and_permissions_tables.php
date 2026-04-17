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
        Schema::create('roles', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name')->unique();
            $blueprint->string('display_name');
            $blueprint->string('description')->nullable();
            $blueprint->boolean('is_system')->default(false);
            $blueprint->timestamps();
        });

        Schema::create('permissions', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name')->unique(); // e.g. pelanggan.view
            $blueprint->string('display_name');  // e.g. Lihat Pelanggan
            $blueprint->string('module');        // e.g. pelanggan
            $blueprint->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $blueprint) {
            $blueprint->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $blueprint->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $blueprint->primary(['permission_id', 'role_id']);
        });

        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->foreignId('role_id')->nullable()->after('role')->constrained('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['role_id']);
            $blueprint->dropColumn('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
