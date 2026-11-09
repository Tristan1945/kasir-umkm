<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            ['nama_produk' => 'Nasi Goreng Spesial', 'harga' => 18000, 'stok' => 25, 'kategori' => 'Makanan', 'gambar' => 'nasgor.jpg'],
            ['nama_produk' => 'Mie Ayam Bakso', 'harga' => 16000, 'stok' => 20, 'kategori' => 'Makanan', 'gambar' => 'mie-ayam-bakso.png'],
            ['nama_produk' => 'Sate Ayam', 'harga' => 20000, 'stok' => 18, 'kategori' => 'Makanan', 'gambar' => 'sate.webp'],
            ['nama_produk' => 'Gorengan', 'harga' => 5000, 'stok' => 40, 'kategori' => 'Snack', 'gambar' => 'gorengan.jpg'],
            ['nama_produk' => 'Es Teh Manis', 'harga' => 5000, 'stok' => 50, 'kategori' => 'Minuman', 'gambar' => 'esteh.jpg'],
            ['nama_produk' => 'Es Jeruk Segar', 'harga' => 8000, 'stok' => 30, 'kategori' => 'Minuman', 'gambar' => 'es-jeruk.jpg'],
            ['nama_produk' => 'Kopi Susu Gula Aren', 'harga' => 15000, 'stok' => 25, 'kategori' => 'Minuman', 'gambar' => null],
            ['nama_produk' => 'Pisang Goreng Cokelat', 'harga' => 12000, 'stok' => 22, 'kategori' => 'Snack', 'gambar' => 'pisang.jpeg'],
            ['nama_produk' => 'Seblak Komplit', 'harga' => 18000, 'stok' => 15, 'kategori' => 'Makanan', 'gambar' => 'seblak.jpg'],
            ['nama_produk' => 'Bakso Mercon', 'harga' => 25000, 'stok' => 12, 'kategori' => 'Makanan', 'gambar' => 'bakso.webp'],
        ];

        foreach ($produk as $data) {
            Produk::updateOrCreate(['nama_produk' => $data['nama_produk']], $data);
        }
    }
}
