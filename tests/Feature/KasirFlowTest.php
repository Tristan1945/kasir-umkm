<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rejects_insufficient_payment_without_creating_transaction(): void
    {
        $produk = Produk::create([
            'nama_produk' => 'Nasi Goreng',
            'harga' => 18000,
            'stok' => 5,
            'kategori' => 'Makanan',
            'gambar' => 'nasgor.jpg',
        ]);
        $this->withSession(['cart' => [
            $produk->id => ['nama_produk' => $produk->nama_produk, 'harga' => $produk->harga, 'gambar' => $produk->gambar, 'jumlah' => 1],
        ]]);

        $response = $this->from('/')->post('/checkout', ['_token' => csrf_token(), 'uang_bayar' => 10000]);

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Uang bayar belum mencukupi total belanja.');
        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 5]);
    }

    public function test_checkout_uses_current_product_price_and_reduces_stock(): void
    {
        $produk = Produk::create([
            'nama_produk' => 'Mie Ayam',
            'harga' => 16000,
            'stok' => 4,
            'kategori' => 'Makanan',
        ]);
        $this->withSession(['cart' => [
            $produk->id => ['nama_produk' => $produk->nama_produk, 'harga' => 1000, 'jumlah' => 2],
        ]]);

        $response = $this->post('/checkout', ['_token' => csrf_token(), 'uang_bayar' => 40000]);

        $response->assertOk();
        $this->assertDatabaseHas('transaksis', ['total_harga' => 32000, 'uang_bayar' => 40000, 'kembalian' => 8000]);
        $this->assertDatabaseHas('detail_transaksis', ['produk_id' => $produk->id, 'jumlah' => 2, 'subtotal' => 32000]);
        $this->assertDatabaseHas('produks', ['id' => $produk->id, 'stok' => 2]);
        $this->assertDatabaseCount('transaksis', 1);
        $this->assertDatabaseCount('detail_transaksis', 1);
        self::assertFalse(session()->has('cart'));
    }

    public function test_product_index_removes_unavailable_cart_items(): void
    {
        $produk = Produk::create([
            'nama_produk' => 'Es Teh',
            'harga' => 5000,
            'stok' => 0,
            'kategori' => 'Minuman',
        ]);
        $this->withSession(['cart' => [
            $produk->id => ['nama_produk' => $produk->nama_produk, 'harga' => $produk->harga, 'jumlah' => 1],
        ]]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewHas('cart', []);
        self::assertSame([], session('cart'));
    }
}
