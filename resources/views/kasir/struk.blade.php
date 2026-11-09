@extends('layouts.app')

@section('title', 'Struk '.$transaksi->kode_transaksi)

@section('content')
<div class="row justify-content-center"><div class="col-md-7 col-lg-5">
    <div class="receipt card overflow-hidden" id="struk">
        <div class="receipt-head text-center p-4"><div class="receipt-logo mx-auto mb-3"><i class="fa-solid fa-store"></i></div><h3 class="fw-bold mb-1">Kasir<span class="text-primary">UMKM</span></h3><p class="text-secondary small mb-0">Struk pembayaran resmi</p></div>
        <div class="p-4">
            <div class="d-flex justify-content-between small text-secondary mb-4"><span><i class="fa-solid fa-hashtag me-1"></i>{{ $transaksi->kode_transaksi }}</span><span><i class="fa-regular fa-clock me-1"></i>{{ $transaksi->created_at->format('d M Y, H:i') }}</span></div>
            @foreach ($transaksi->detailTransaksis as $detail)
                <div class="d-flex justify-content-between mb-3"><span>{{ $detail->produk->nama_produk }}<small class="d-block text-secondary mt-1">{{ $detail->jumlah }} x Rp {{ number_format($detail->produk->harga, 0, ',', '.') }}</small></span><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></div>
            @endforeach
            <hr><div class="d-flex justify-content-between fw-bold fs-5 mb-3"><span>Total</span><span class="text-primary">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></div><div class="d-flex justify-content-between text-secondary mb-1"><span>Uang bayar</span><span>Rp {{ number_format($transaksi->uang_bayar, 0, ',', '.') }}</span></div><div class="d-flex justify-content-between text-success fw-semibold"><span>Kembalian</span><span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span></div>
            <div class="receipt-code text-center my-4"><i class="fa-solid fa-qrcode fs-1"></i><small class="d-block text-secondary mt-2">Simpan struk ini sebagai bukti pembayaran</small></div>
            <p class="text-center text-secondary small mb-0"><i class="fa-regular fa-heart text-danger me-1"></i>Terima kasih sudah berbelanja</p>
        </div>
    </div>
    <div class="d-flex gap-2 mt-3"><button onclick="window.print()" class="btn btn-primary flex-grow-1 rounded-3"><i class="fa-solid fa-print me-2"></i>Cetak Struk</button><a href="{{ route('kasir.index') }}" class="btn btn-light border rounded-3"><i class="fa-solid fa-plus me-1"></i>Transaksi Baru</a></div>
</div></div>
@endsection

@push('styles')
<style>
    .receipt-head { background:linear-gradient(135deg,#eef2ff,#fff);border-bottom:1px dashed #cbd5e1; }
    .receipt-logo { width:54px;height:54px;display:grid;place-items:center;border-radius:16px;background:#6366f1;color:#fff;font-size:1.35rem; }
    .receipt-code { border:1px dashed #cbd5e1;border-radius:14px;padding:14px;color:#64748b; }
    @media print { .topbar,.btn,main > .alert { display:none!important; } body { background:#fff; } .receipt { box-shadow:none;border:0; } }
</style>
@endpush
