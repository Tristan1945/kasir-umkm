@extends('layouts.app')

@section('title', 'Kasir UMKM - Penjualan')

@section('content')
@php
    $total = collect($cart)->sum(fn ($item) => $item['harga'] * $item['jumlah']);
    $jumlahItem = collect($cart)->sum('jumlah');
    $kategori = ['Semua', 'Makanan', 'Minuman', 'Snack'];
    $categoryIcons = ['Semua' => 'fa-grid-2', 'Makanan' => 'fa-utensils', 'Minuman' => 'fa-mug-hot', 'Snack' => 'fa-cookie-bite'];
    $categoryColors = ['Makanan' => 'primary', 'Minuman' => 'info', 'Snack' => 'warning', 'Camilan' => 'warning'];
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><p class="text-uppercase text-brand fw-bold small mb-2" style="letter-spacing:.12em">Point of sale</p><h1 class="fw-bold mb-1" style="letter-spacing:-.04em">Mulai transaksi</h1><p class="text-secondary mb-0">Kelola pesanan pelanggan dengan cepat dan rapi.</p></div>
    <button class="btn btn-primary rounded-3 d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer"><i class="fa-solid fa-basket-shopping me-2"></i>Keranjang <span class="badge bg-white text-primary ms-1">{{ $jumlahItem }}</span></button>
</div>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="card p-3 p-md-4 mb-3">
            <div class="row g-2">
                <div class="col-md-7"><div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span><input id="productSearch" class="form-control border-start-0 ps-0" placeholder="Cari nama produk..."></div></div>
                <div class="col-md-5 d-flex align-items-center justify-content-md-end"><span class="small text-secondary"><i class="fa-solid fa-boxes-stacked me-1"></i>{{ $produks->count() }} produk tersedia</span></div>
            </div>
            <div class="d-flex gap-2 overflow-auto pt-3" id="categoryFilters">
                @foreach ($kategori as $item)
                    <button class="category-filter btn btn-sm rounded-pill px-3 {{ $loop->first ? 'btn-primary' : 'btn-soft' }}" data-category="{{ $item }}"><i class="fa-solid {{ $categoryIcons[$item] }} me-1"></i>{{ $item }}</button>
                @endforeach
            </div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3" id="productGrid">
            @forelse ($produks as $produk)
                <div class="col product-item" data-category="{{ $produk->kategori }}" data-name="{{ strtolower($produk->nama_produk) }}">
                    <div class="card product-card h-100 overflow-hidden product-detail-trigger" role="button" tabindex="0" data-name="{{ $produk->nama_produk }}" data-category="{{ $produk->kategori }}" data-price="Rp {{ number_format($produk->harga, 0, ',', '.') }}" data-stock="{{ $produk->stok }}" data-image="{{ $produk->gambar ? '/'.ltrim($produk->gambar, '/') : '' }}" data-action="{{ route('kasir.add', $produk->id) }}">
                        <div class="product-image-wrap position-relative">
                            @if ($produk->gambar)
                                <img src="{{ '/'.ltrim($produk->gambar, '/') }}" alt="{{ $produk->nama_produk }}" class="product-image" loading="lazy">
                            @else
                                <div class="product-image product-placeholder"><i class="fa-solid {{ $produk->kategori === 'Minuman' ? 'fa-mug-hot' : ($produk->kategori === 'Snack' || $produk->kategori === 'Camilan' ? 'fa-cookie-bite' : 'fa-utensils') }}"></i></div>
                            @endif
                            <span class="badge bg-{{ $categoryColors[$produk->kategori] ?? 'secondary' }} position-absolute top-0 end-0 m-3">{{ $produk->kategori }}</span>
                        </div>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold mb-1 product-name">{{ $produk->nama_produk }}</h5>
                            <div class="d-flex justify-content-between align-items-end mt-auto pt-3"><div><strong class="text-brand">Rp {{ number_format($produk->harga, 0, ',', '.') }}</strong><small class="d-block text-secondary mt-1"><i class="fa-solid fa-layer-group me-1"></i>{{ $produk->stok }} stok</small></div><form method="POST" action="{{ route('kasir.add', $produk->id) }}">@csrf<button type="submit" class="btn btn-primary rounded-3 add-button" @disabled($produk->stok < 1)><i class="fa-solid fa-plus"></i></button></form></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="card p-5 text-center text-secondary"><i class="fa-solid fa-box-open fs-1 mb-3"></i><div>Belum ada produk. Jalankan seeder terlebih dahulu.</div></div></div>
            @endforelse
        </div>
        <div id="emptySearch" class="card p-5 text-center text-secondary d-none"><i class="fa-solid fa-magnifying-glass fs-1 mb-3"></i><div>Produk tidak ditemukan.</div></div>
    </section>
    <aside class="col-lg-4 d-none d-lg-block">@include('kasir.partials.cart', ['cart' => $cart, 'total' => $total, 'jumlahItem' => $jumlahItem, 'cartContext' => 'desktop'])</aside>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel"><div class="offcanvas-header border-bottom"><h5 class="offcanvas-title fw-bold" id="cartDrawerLabel"><i class="fa-solid fa-basket-shopping text-brand me-2"></i>Keranjang</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div><div class="offcanvas-body p-0">@include('kasir.partials.cart', ['cart' => $cart, 'total' => $total, 'jumlahItem' => $jumlahItem, 'cartContext' => 'mobile'])</div></div>
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 position-absolute top-0 end-0 z-1">
                <button type="button" class="btn-close bg-white rounded-circle p-2" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div id="productDetailImage" class="product-detail-image"></div>
            <div class="p-4">
                <span id="productDetailCategory" class="badge bg-primary-subtle text-primary mb-2"></span>
                <h4 id="productDetailTitle" class="fw-bold mb-2"></h4>
                <div id="productDetailPrice" class="text-brand fw-bold fs-5 mb-1"></div>
                <p id="productDetailStock" class="text-secondary small mb-4"></p>
                <form id="productDetailForm" method="POST">@csrf
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold" id="productDetailSubmit"><i class="fa-solid fa-basket-shopping me-2"></i>Tambah ke Keranjang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-card { transition:transform .22s ease,box-shadow .22s ease; }
    .product-card:hover { transform:translateY(-4px);box-shadow:0 18px 35px rgba(30,41,59,.12); }
    .product-detail-trigger { cursor:pointer; }
    .product-detail-trigger:focus-visible { outline:3px solid rgba(99,102,241,.35);outline-offset:3px; }
    .product-image-wrap { height:170px;background:linear-gradient(135deg,#eef2ff,#f8fafc);overflow:hidden; }
    .product-image { width:100%;height:100%;object-fit:cover;transition:transform .35s ease; }
    .product-card:hover .product-image { transform:scale(1.05); }
    .confirm-icon { width:48px;height:48px;display:grid;place-items:center;border-radius:14px;background:#eef2ff;color:#6366f1;font-size:1.2rem; }
    .product-placeholder { display:grid;place-items:center;color:#6366f1;font-size:3rem;background:linear-gradient(135deg,#e0e7ff,#f5f3ff); }
    .product-detail-image { height:210px;background:linear-gradient(135deg,#e0e7ff,#f5f3ff) center/cover;display:grid;place-items:center;color:#6366f1;font-size:3.5rem; }
    .product-detail-image img { width:100%;height:100%;object-fit:cover; }
    .add-button { width:38px;height:38px;display:grid;place-items:center; }
    .category-filter { white-space:nowrap; }
    .cart-item { animation:cartIn .3s ease both; }
    @keyframes cartIn { from { opacity:0;transform:translateX(10px); } to { opacity:1;transform:translateX(0); } }
    @media (max-width:575.98px) { .product-image-wrap { height:150px; } }
</style>
@endpush

@push('scripts')
<script>
    const search = document.getElementById('productSearch');
    const filters = document.querySelectorAll('.category-filter');
    const products = document.querySelectorAll('.product-item');
    const emptySearch = document.getElementById('emptySearch');
    let activeCategory = 'Semua';
    function filterProducts() {
        const query = (search.value || '').toLowerCase().trim();
        let visible = 0;
        products.forEach((product) => {
            const categoryMatches = activeCategory === 'Semua' || product.dataset.category === activeCategory || (activeCategory === 'Snack' && product.dataset.category === 'Camilan');
            const matches = categoryMatches && product.dataset.name.includes(query);
            product.classList.toggle('d-none', !matches);
            if (matches) visible++;
        });
        emptySearch.classList.toggle('d-none', visible !== 0);
    }
    search.addEventListener('input', filterProducts);
    filters.forEach((filter) => filter.addEventListener('click', () => {
        activeCategory = filter.dataset.category;
        filters.forEach((item) => item.classList.replace('btn-primary', 'btn-soft'));
        filter.classList.replace('btn-soft', 'btn-primary');
        filterProducts();
    }));
    const detailModalElement = document.getElementById('productDetailModal');
    const detailModal = new bootstrap.Modal(detailModalElement);
    const detailImage = document.getElementById('productDetailImage');
    const detailCategory = document.getElementById('productDetailCategory');
    const detailTitle = document.getElementById('productDetailTitle');
    const detailPrice = document.getElementById('productDetailPrice');
    const detailStock = document.getElementById('productDetailStock');
    const detailForm = document.getElementById('productDetailForm');
    const detailSubmit = document.getElementById('productDetailSubmit');

    function openProductDetail(product) {
        const image = product.dataset.image;
        const categoryIcon = product.dataset.category === 'Minuman' ? 'fa-mug-hot' : (product.dataset.category === 'Snack' || product.dataset.category === 'Camilan' ? 'fa-cookie-bite' : 'fa-utensils');
        if (image) {
            const imageElement = document.createElement('img');
            imageElement.src = image;
            imageElement.alt = product.dataset.name;
            imageElement.className = 'w-100 h-100 object-fit-cover';
            detailImage.replaceChildren(imageElement);
        } else {
            const iconElement = document.createElement('i');
            iconElement.className = `fa-solid ${categoryIcon}`;
            detailImage.replaceChildren(iconElement);
        }
        detailCategory.textContent = product.dataset.category;
        detailTitle.textContent = product.dataset.name;
        detailPrice.textContent = product.dataset.price;
        detailStock.textContent = `Stok tersedia: ${product.dataset.stock}`;
        detailForm.action = product.dataset.action;
        detailSubmit.disabled = Number(product.dataset.stock) < 1;
        detailSubmit.innerHTML = Number(product.dataset.stock) < 1 ? '<i class="fa-solid fa-circle-xmark me-2"></i>Stok habis' : '<i class="fa-solid fa-basket-shopping me-2"></i>Tambah ke Keranjang';
        detailModal.show();
    }

    document.querySelectorAll('.product-detail-trigger').forEach((product) => {
        product.addEventListener('click', (event) => {
            if (event.target.closest('form, button')) { return; }
            openProductDetail(product);
        });
        product.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openProductDetail(product);
            }
        });
    });
    document.querySelectorAll('form[action*="checkout"]').forEach((form) => form.addEventListener('submit', (event) => {
        if (event.defaultPrevented) { return; }
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    }));
</script>
@endpush
