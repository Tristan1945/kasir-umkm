<div class="card cart-panel overflow-hidden">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center"><div><h4 class="fw-bold mb-1">Keranjang</h4><small class="text-secondary">{{ $jumlahItem }} item dipilih</small></div><span class="rounded-circle bg-primary-subtle text-primary p-2"><i class="fa-solid fa-basket-shopping"></i></span></div>
    <div class="p-4">
        @forelse ($cart as $id => $item)
            <div class="cart-item d-flex gap-2 align-items-center mb-3">
                <div class="cart-thumb">@if (!empty($item['gambar']))<img src="{{ asset($item['gambar']) }}" alt="{{ $item['nama_produk'] }}">@else<i class="fa-solid fa-utensils"></i>@endif</div>
                <div class="flex-grow-1 min-w-0"><div class="fw-semibold text-truncate">{{ $item['nama_produk'] }}</div><small class="text-secondary">{{ $item['jumlah'] }} x Rp {{ number_format($item['harga'], 0, ',', '.') }}</small><strong class="d-block small mt-1">Rp {{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}</strong></div>
                <form method="POST" action="{{ route('kasir.update', $id) }}" class="d-flex gap-1">@csrf<input type="number" name="jumlah" value="{{ $item['jumlah'] }}" min="1" class="form-control form-control-sm text-center cart-qty"><button class="btn btn-sm btn-light border" title="Simpan jumlah"><i class="fa-solid fa-check text-success"></i></button></form>
                <form method="POST" action="{{ route('kasir.remove', $id) }}">@csrf<button class="btn btn-sm btn-link text-danger px-1" title="Hapus"><i class="fa-solid fa-trash-can"></i></button></form>
            </div>
        @empty
            <div class="text-center text-secondary py-5"><i class="fa-solid fa-basket-shopping fs-1 mb-3 text-secondary opacity-50"></i><p class="mb-0">Keranjang masih kosong</p><small>Pilih produk untuk memulai.</small></div>
        @endforelse
        <hr class="my-4">
        <div class="d-flex justify-content-between align-items-center mb-3"><span class="text-secondary">Total pembayaran</span><strong class="fs-5">Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
        <form method="POST" action="{{ route('kasir.checkout') }}" class="checkout-form" data-total="{{ $total }}">@csrf<label class="form-label fw-semibold small" for="uang_bayar-{{ $cartContext }}">Uang dibayar</label><div class="input-group input-group-lg mb-2"><span class="input-group-text bg-white">Rp</span><input type="text" class="form-control payment-input" id="uang_bayar-{{ $cartContext }}" name="uang_bayar" value="{{ old('uang_bayar') ? number_format((int) old('uang_bayar'), 0, ',', '.') : '' }}" placeholder="0" inputmode="numeric" autocomplete="off" required></div><div class="payment-notice small mb-3" role="status" aria-live="polite"></div><button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-semibold" @disabled($total < 1)><i class="fa-solid fa-receipt me-2"></i>Bayar & Cetak Struk</button></form>
    </div>
</div>
@pushOnce('styles')
<style>
    .cart-panel { position:sticky;top:82px; }
    .cart-thumb { width:48px;height:48px;flex:none;border-radius:12px;overflow:hidden;background:#eef2ff;display:grid;place-items:center;color:#6366f1; }
    .cart-thumb img { width:100%;height:100%;object-fit:cover; }
    .cart-qty { width:48px; }
    .offcanvas { max-width: min(100%, 430px); }
    .payment-notice { min-height: 1.25rem; }
    @media (max-width:991.98px) { .offcanvas .cart-panel { border:0;box-shadow:none;border-radius:0;position:static; } }
</style>
@endPushOnce
@pushOnce('scripts')
<script>
    document.querySelectorAll('.checkout-form').forEach((form) => {
        const input = form.querySelector('.payment-input');
        const notice = form.querySelector('.payment-notice');
        const total = Number(form.dataset.total);
        let noticeTimer;

        const formatRupiah = (value) => {
            const digits = value.replace(/\D/g, '');
            return digits ? Number(digits).toLocaleString('id-ID') : '';
        };

        const showNotice = (message, type = 'danger') => {
            window.clearTimeout(noticeTimer);
            notice.className = `payment-notice small mb-3 text-${type}`;
            const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
            notice.innerHTML = `<i class="fa-solid ${icon} me-1"></i>${message}`;
            noticeTimer = window.setTimeout(() => {
                notice.className = 'payment-notice small mb-3';
                notice.textContent = '';
            }, 3000);
        };

        const showCheckoutConfirmation = (amount) => {
            const modalElement = document.createElement('div');
            modalElement.className = 'modal fade';
            modalElement.tabIndex = -1;
            modalElement.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-body text-center p-4">
                            <div class="confirm-icon mx-auto mb-3"><i class="fa-solid fa-receipt"></i></div>
                            <h5 class="fw-bold mb-2">Selesaikan pembayaran?</h5>
                            <p class="text-secondary small mb-4">Pastikan nominal pembayaran sudah benar.</p>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light border w-50 rounded-3" data-confirm-cancel>Batal</button>
                                <button type="button" class="btn btn-primary w-50 rounded-3" data-confirm-submit>Lanjutkan</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalElement);

            const modal = new bootstrap.Modal(modalElement);
            const cleanUp = () => {
                modal.dispose();
                modalElement.remove();
            };

            modalElement.querySelector('[data-confirm-cancel]').addEventListener('click', () => {
                cleanUp();
            });
            modalElement.querySelector('[data-confirm-submit]').addEventListener('click', () => {
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                input.value = String(amount);
                cleanUp();
                form.submit();
            });
            modalElement.addEventListener('hidden.bs.modal', cleanUp, { once: true });
            modal.show();
        };

        input.addEventListener('input', () => {
            input.value = formatRupiah(input.value);
            const amount = Number(input.value.replace(/\D/g, '')) || 0;

            if (amount > 0 && amount < total) {
                showNotice(`Nominal kurang Rp ${formatRupiah(String(total - amount))}.`, 'danger');
            } else if (amount >= total && total > 0) {
                showNotice('Nominal sudah mencukupi.', 'success');
            } else {
                notice.className = 'payment-notice small mb-3';
                notice.textContent = '';
            }
        });

        form.addEventListener('submit', (event) => {
            const amount = Number(input.value.replace(/\D/g, '')) || 0;

            if (amount < total) {
                event.preventDefault();
                showNotice(`Pembayaran kurang Rp ${formatRupiah(String(total - amount))}.`, 'danger');
                return;
            }

            input.value = String(amount);
            event.preventDefault();
            input.value = formatRupiah(String(amount));
            showCheckoutConfirmation(amount);
        });
    });
</script>
@endPushOnce
