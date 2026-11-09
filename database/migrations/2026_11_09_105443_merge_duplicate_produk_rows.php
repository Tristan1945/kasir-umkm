<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $gambarProduk = [
            'Nasi Goreng Spesial' => 'nasgor.jpg',
            'Mie Ayam Bakso' => 'mie-ayam-bakso.png',
            'Sate Ayam' => 'sate.webp',
            'Gorengan' => 'gorengan.jpg',
            'Es Teh Manis' => 'esteh.jpg',
            'Es Jeruk Segar' => 'es-jeruk.jpg',
            'Kopi Susu Gula Aren' => null,
            'Pisang Goreng Cokelat' => 'pisang.jpeg',
            'Seblak Komplit' => 'seblak.jpg',
            'Bakso Mercon' => 'bakso.webp',
        ];

        DB::transaction(function () use ($gambarProduk): void {
            foreach ($gambarProduk as $namaProduk => $gambar) {
                $produk = DB::table('produks')
                    ->where('nama_produk', $namaProduk)
                    ->orderBy('id')
                    ->get();

                if ($produk->isEmpty()) {
                    continue;
                }

                $produkUtama = $produk->first();
                DB::table('produks')->where('id', $produkUtama->id)->update(['gambar' => $gambar]);

                foreach ($produk->skip(1) as $duplikat) {
                    DB::table('detail_transaksis')
                        ->where('produk_id', $duplikat->id)
                        ->update(['produk_id' => $produkUtama->id]);
                    DB::table('produks')->where('id', $duplikat->id)->delete();
                }
            }
        });
    }

    public function down(): void {}
};
