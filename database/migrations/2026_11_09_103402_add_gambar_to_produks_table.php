<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('kategori');
        });

        DB::table('produks')->where('nama_produk', 'Nasi Goreng Spesial')->update(['gambar' => 'nasgor.jpg']);
        DB::table('produks')->where('nama_produk', 'Mie Ayam Bakso')->update(['gambar' => 'mie-ayam-bakso.png']);
        DB::table('produks')->where('nama_produk', 'Sate Ayam')->update(['gambar' => 'sate.webp']);
        DB::table('produks')->where('nama_produk', 'Gorengan')->update(['gambar' => 'gorengan.jpg']);
        DB::table('produks')->where('nama_produk', 'Es Teh Manis')->update(['gambar' => 'esteh.jpg']);
        DB::table('produks')->where('nama_produk', 'Es Jeruk Segar')->update(['gambar' => 'es-jeruk.jpg']);
        DB::table('produks')->where('nama_produk', 'Pisang Goreng Cokelat')->update(['gambar' => 'pisang.jpeg']);
        DB::table('produks')->where('nama_produk', 'Seblak Komplit')->update(['gambar' => 'seblak.jpg']);
        DB::table('produks')->where('nama_produk', 'Bakso Mercon')->update(['gambar' => 'bakso.webp']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
