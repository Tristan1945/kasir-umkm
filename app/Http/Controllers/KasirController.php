<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class KasirController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);
        if ($cart !== []) {
            $products = Produk::whereIn('id', array_keys($cart))->get()->keyBy('id');

            foreach ($cart as $id => &$item) {
                $produk = $products->get($id);
                if ($produk === null || $produk->stok < 1) {
                    unset($cart[$id]);

                    continue;
                }

                $item['nama_produk'] = $produk->nama_produk;
                $item['harga'] = $produk->harga;
                $item['gambar'] = $produk->gambar;
                $item['jumlah'] = min((int) ($item['jumlah'] ?? 1), $produk->stok);
            }
            unset($item);
            session(['cart' => $cart]);
        }

        return view('kasir.index', [
            'produks' => Produk::orderBy('kategori')->orderBy('nama_produk')->get(),
            'cart' => $cart,
        ]);
    }

    public function addToCart(int $id): RedirectResponse
    {
        $produk = Produk::findOrFail($id);
        $cart = session('cart', []);
        $jumlahSaatIni = $cart[$id]['jumlah'] ?? 0;

        if ($jumlahSaatIni >= $produk->stok) {
            return back()->with('error', "Stok {$produk->nama_produk} tidak mencukupi.");
        }

        $cart[$id] = [
            'nama_produk' => $produk->nama_produk,
            'harga' => $produk->harga,
            'gambar' => $produk->gambar,
            'jumlah' => $jumlahSaatIni + 1,
        ];
        session(['cart' => $cart]);

        return back()->with('success', "{$produk->nama_produk} ditambahkan ke keranjang.");
    }

    public function removeFromCart(int $id): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function updateCart(Request $request, int $id): RedirectResponse
    {
        $request->validate(['jumlah' => ['required', 'integer', 'min:1']]);
        $produk = Produk::findOrFail($id);

        if ($request->integer('jumlah') > $produk->stok) {
            return back()->with('error', "Jumlah {$produk->nama_produk} melebihi stok tersedia.");
        }

        $cart = session('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['jumlah'] = $request->integer('jumlah');
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $cart = session('cart', []);
        if ($cart === []) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $validated = $request->validate([
            'uang_bayar' => ['required', 'integer', 'min:0'],
        ]);
        try {
            $transaksi = DB::transaction(function () use ($cart, $validated): Transaksi {
                $produkTerkunci = [];
                $totalHarga = 0;
                foreach ($cart as $id => $item) {
                    if (! is_array($item) || ! isset($item['jumlah']) || ! is_numeric($item['jumlah']) || (int) $item['jumlah'] < 1) {
                        throw new \RuntimeException('Isi keranjang tidak valid. Silakan buat transaksi baru.');
                    }

                    $produk = Produk::lockForUpdate()->find($id);
                    $jumlah = (int) $item['jumlah'];
                    if ($produk === null || $produk->stok < $jumlah) {
                        throw new \RuntimeException('Stok salah satu produk berubah. Silakan periksa kembali keranjang.');
                    }
                    $produkTerkunci[$id] = $produk;
                    $totalHarga += $produk->harga * $jumlah;
                }

                if ($validated['uang_bayar'] < $totalHarga) {
                    throw new \RuntimeException('Uang bayar belum mencukupi total belanja.');
                }

                $transaksi = Transaksi::create([
                    'kode_transaksi' => 'TRX-'.now()->format('YmdHis').'-'.random_int(100, 999),
                    'total_harga' => $totalHarga,
                    'uang_bayar' => $validated['uang_bayar'],
                    'kembalian' => $validated['uang_bayar'] - $totalHarga,
                ]);

                foreach ($cart as $id => $item) {
                    $jumlah = (int) $item['jumlah'];
                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $id,
                        'jumlah' => $jumlah,
                        'subtotal' => $produkTerkunci[$id]->harga * $jumlah,
                    ]);
                    $produkTerkunci[$id]->decrement('stok', $jumlah);
                }

                return $transaksi;
            });
        } catch (Throwable $exception) {
            report($exception);

            $message = $exception instanceof \RuntimeException
                ? $exception->getMessage()
                : 'Transaksi gagal diproses. Silakan coba lagi.';

            return back()->withInput()->with('error', $message);
        }

        session()->forget('cart');

        return view('kasir.struk', [
            'transaksi' => $transaksi->load('detailTransaksis.produk'),
        ]);
    }
}
