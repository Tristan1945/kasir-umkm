<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('produks')
            ->where('nama_produk', 'Kopi Susu Gula Aren')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data produk yang dihapus tidak dipulihkan saat rollback.
    }
};
