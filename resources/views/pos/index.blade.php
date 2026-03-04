@extends('layouts.app')

@section('title', 'Point of Sales - Simple Akunting')

@section('content')
<style>
    .pos-container { display: flex; gap: 1rem; height: calc(100vh - 200px); min-height: 400px; }
    .pos-products { flex: 0 0 55%; max-width: 55%; display: flex; flex-direction: column; }
    .pos-cart { flex: 0 0 calc(45% - 1rem); max-width: calc(45% - 1rem); display: flex; flex-direction: column; }
    .pos-search { position: relative; margin-bottom: 0; }
    .pos-search input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; background: #fff; color: #1e293b; }
    .pos-search input:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.1); }
    .pos-search input::placeholder { color: #94a3b8; }
    .search-results { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; max-height: 350px; overflow-y: auto; z-index: 100; display: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
    .search-results.show { display: block; }
    .search-item { padding: 10px 14px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
    .search-item:hover { background: #f1f5f9; }
    .search-item:last-child { border-bottom: none; }
    .search-item-name { font-weight: 600; color: #0f172a; }
    .search-item-info { font-size: 0.8rem; color: #64748b; }
    .search-item-price { font-weight: 700; color: #7c3aed; }
    .cart-card { background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; flex: 1; display: flex; flex-direction: column; overflow: hidden; color: #1e293b; min-height: 0; }
    .cart-header { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; flex-shrink: 0; }
    .cart-header h5 { margin: 0; font-weight: 700; color: #0f172a; font-size: 0.95rem; }
    .cart-items { flex: 1; overflow-y: auto; padding: 0; background: #fff; min-height: 0; }
    .cart-item { padding: 8px 14px; border-bottom: 1px solid #e2e8f0; display: flex; gap: 10px; align-items: center; }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name { font-weight: 600; font-size: 0.85rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cart-item-price { font-size: 0.75rem; color: #475569; font-weight: 500; }
    .cart-item-qty { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
    .cart-item-qty button { width: 26px; height: 26px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; cursor: pointer; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; color: #334155; }
    .cart-item-qty button:hover { background: #e2e8f0; }
    .cart-item-qty span { min-width: 22px; text-align: center; font-weight: 700; color: #0f172a; font-size: 0.9rem; }
    .cart-item-subtotal { font-weight: 700; min-width: 80px; text-align: right; color: #0f172a; font-size: 0.85rem; flex-shrink: 0; }
    .cart-item-remove { background: none; border: none; color: #ef4444; cursor: pointer; padding: 2px; font-size: 1rem; font-weight: 700; flex-shrink: 0; }
    .cart-item-remove:hover { color: #dc2626; }
    .cart-footer { padding: 12px 14px; border-top: 2px solid #e2e8f0; background: #f8fafc; flex-shrink: 0; }
    .cart-total { display: flex; justify-content: space-between; align-items: center; font-size: 1.15rem; font-weight: 800; margin-bottom: 8px; color: #0f172a; }
    .cart-total .amount { color: #7c3aed; }
    .btn-pay { width: 100%; padding: 12px; border: none; border-radius: 10px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #ffffff; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
    .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(139,92,246,0.4); }
    .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }
    .mode-toggle { display: flex; gap: 4px; padding: 3px; background: #f1f5f9; border-radius: 10px; margin-bottom: 0.75rem; }
    .mode-toggle button { flex: 1; padding: 8px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; background: transparent; color: #64748b; font-size: 0.9rem; }
    .mode-toggle button.active { background: #ffffff; color: #7c3aed; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .supplier-select { margin-bottom: 0.75rem; display: none; }
    .supplier-select.show { display: block; }
    .payment-modal .modal-content { border-radius: 16px; color: #1e293b; }
    .empty-cart { text-align: center; padding: 24px 16px; color: #94a3b8; }
    .empty-cart .icon { font-size: 2.5rem; margin-bottom: 8px; }
    .empty-cart p { color: #64748b; font-weight: 500; margin-bottom: 2px; }
    .empty-cart small { color: #94a3b8; font-size: 0.8rem; }
    .session-badge { background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; padding: 5px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
    .diskon-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 0.85rem; color: #334155; }
    .diskon-input { width: 100px; padding: 3px 6px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: right; color: #0f172a; background: #fff; font-size: 0.85rem; }
    .cart-footer .form-select { color: #334155; background-color: #fff; font-size: 0.85rem; padding: 4px 8px; }
    @media (max-width: 768px) {
        .pos-container { flex-direction: column; height: auto; }
        .pos-products, .pos-cart { flex: none; max-width: 100%; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">🛒 Point of Sales</h1>
    <span class="session-badge">Shift aktif sejak {{ $session->opened_at->format('H:i') }}</span>
</div>

<!-- Mode Toggle -->
<div class="mode-toggle">
    <button id="btnModeSale" class="active" onclick="switchMode('sale')">💰 Penjualan</button>
    @if(auth()->user()->canAccessPosBuying())
    <button id="btnModeBuy" onclick="switchMode('buy')">📥 Pembelian</button>
    @endif
</div>

<!-- Supplier Select (Buying mode only) -->
<div class="supplier-select" id="supplierSection">
    <div class="row g-2">
        <div class="col-md-4">
            <select id="id_pemasok" class="form-select">
                <option value="">-- Pilih Pemasok --</option>
                @foreach($pemasok as $p)
                    <option value="{{ $p->id_pemasok }}">{{ $p->nama_pemasok }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select id="metode_pembayaran_buy" class="form-select">
                <option value="Tunai">Tunai</option>
                <option value="Kredit">Kredit (Hutang Supplier)</option>
            </select>
        </div>
    </div>
</div>

<div class="pos-container">
    <!-- Left: Product Search -->
    <div class="pos-products">
        <div class="pos-search">
            <input type="text" id="searchInput" placeholder="🔍 Scan barcode atau ketik nama produk..." autofocus>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="pos-cart">
        <div class="cart-card">
            <div class="cart-header">
                <h5 id="cartTitle">🛒 Keranjang</h5>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">Hapus Semua</button>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="empty-cart" id="emptyCart">
                    <div class="icon">📦</div>
                    <p>Keranjang kosong</p>
                    <small>Scan barcode atau cari produk</small>
                </div>
            </div>
            <div class="cart-footer">
                <div class="diskon-row" id="diskonRow">
                    <span>Diskon:</span>
                    <input type="number" class="diskon-input" id="diskonInput" value="0" min="0" onchange="updateTotal()">
                </div>
                <div class="cart-total">
                    <span>TOTAL</span>
                    <span class="amount" id="cartTotal">Rp 0</span>
                </div>
                <div class="mb-2">
                    <select id="akun_kas_bank" class="form-select form-select-sm">
                        @foreach($akunKas as $kas)
                            <option value="{{ $kas->kode_akun }}" {{ ($perusahaan->pos_akun_kas_default ?? '') == $kas->kode_akun ? 'selected' : '' }}>
                                {{ $kas->kode_akun }} - {{ $kas->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-pay" id="btnPay" onclick="processPayment()" disabled>
                    💳 BAYAR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Success Modal -->
<div class="modal fade payment-modal" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div style="font-size: 3rem; margin-bottom: 16px;">✅</div>
                <h4 id="successTitle">Transaksi Berhasil!</h4>
                <p id="successInfo" class="text-muted"></p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button class="btn btn-outline-primary" id="btnPrintReceipt" onclick="printReceipt()">
                        🖨️ Cetak Struk
                    </button>
                    <button class="btn btn-primary" data-bs-dismiss="modal" onclick="resetForNext()">
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let cart = [];
    let currentMode = 'sale'; // 'sale' or 'buy'
    let lastTransactionId = null;
    let lastTransactionType = null;
    let searchTimeout = null;

    // Mode switching
    function switchMode(mode) {
        currentMode = mode;
        document.getElementById('btnModeSale').classList.toggle('active', mode === 'sale');
        const btnBuy = document.getElementById('btnModeBuy');
        if (btnBuy) btnBuy.classList.toggle('active', mode === 'buy');
        document.getElementById('supplierSection').classList.toggle('show', mode === 'buy');
        document.getElementById('diskonRow').style.display = mode === 'sale' ? 'flex' : 'none';
        document.getElementById('cartTitle').textContent = mode === 'sale' ? '🛒 Keranjang' : '📥 Penerimaan Barang';
        document.getElementById('btnPay').textContent = mode === 'sale' ? '💳 BAYAR' : '📥 SIMPAN PEMBELIAN';
        document.getElementById('searchInput').placeholder = mode === 'sale' 
            ? '🔍 Scan barcode atau ketik nama produk...' 
            : '🔍 Scan barcode barang yang diterima...';
        clearCart();
    }

    // Product search
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 1) {
            document.getElementById('searchResults').classList.remove('show');
            return;
        }
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('pos.search') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(products => {
                    const results = document.getElementById('searchResults');
                    if (products.length === 0) {
                        results.innerHTML = '<div class="search-item"><span class="text-muted">Produk tidak ditemukan</span></div>';
                    } else {
                        results.innerHTML = products.map(p => `
                            <div class="search-item" onclick='addToCart(${JSON.stringify(p)})'>
                                <div>
                                    <div class="search-item-name">${p.nama_barang}</div>
                                    <div class="search-item-info">${p.kode_barang} ${p.barcode ? '| ' + p.barcode : ''} | Stok: ${p.stok_saat_ini} ${p.satuan}</div>
                                </div>
                                <div class="search-item-price">Rp ${formatNumber(currentMode === 'sale' ? p.harga_jual : p.harga_beli)}</div>
                            </div>
                        `).join('');
                    }
                    results.classList.add('show');

                    // Auto-add if exact barcode match (1 result)
                    if (products.length === 1 && products[0].barcode === q) {
                        addToCart(products[0]);
                        document.getElementById('searchInput').value = '';
                        results.classList.remove('show');
                    }
                });
        }, 200);
    });

    // Hide search results on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.pos-search')) {
            document.getElementById('searchResults').classList.remove('show');
        }
    });

    // Add to cart
    function addToCart(product) {
        const existing = cart.find(i => i.id_barang === product.id_barang);
        if (existing) {
            if (currentMode === 'sale' && existing.qty >= product.stok_saat_ini) {
                alert('Stok tidak mencukupi!');
                return;
            }
            existing.qty++;
        } else {
            cart.push({
                id_barang: product.id_barang,
                nama_barang: product.nama_barang,
                kode_barang: product.kode_barang,
                harga: currentMode === 'sale' ? parseFloat(product.harga_jual) : parseFloat(product.harga_beli),
                stok: parseFloat(product.stok_saat_ini),
                satuan: product.satuan,
                qty: 1,
            });
        }
        renderCart();
        document.getElementById('searchInput').value = '';
        document.getElementById('searchInput').focus();
        document.getElementById('searchResults').classList.remove('show');
    }

    // Render cart
    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        
        if (cart.length === 0) {
            container.innerHTML = '<div class="empty-cart" id="emptyCart"><div class="icon">📦</div><p>Keranjang kosong</p><small>Scan barcode atau cari produk</small></div>';
            document.getElementById('btnPay').disabled = true;
        } else {
            container.innerHTML = cart.map((item, idx) => `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.nama_barang}</div>
                        <div class="cart-item-price">Rp ${formatNumber(item.harga)} × ${item.qty}</div>
                    </div>
                    <div class="cart-item-qty">
                        <button onclick="changeQty(${idx}, -1)">−</button>
                        <span>${item.qty}</span>
                        <button onclick="changeQty(${idx}, 1)">+</button>
                    </div>
                    <div class="cart-item-subtotal">Rp ${formatNumber(item.harga * item.qty)}</div>
                    <button class="cart-item-remove" onclick="removeItem(${idx})">✕</button>
                </div>
            `).join('');
            document.getElementById('btnPay').disabled = false;
        }
        updateTotal();
    }

    function changeQty(idx, delta) {
        cart[idx].qty += delta;
        if (currentMode === 'sale' && cart[idx].qty > cart[idx].stok) {
            alert('Stok tidak mencukupi!');
            cart[idx].qty -= delta;
            return;
        }
        if (cart[idx].qty <= 0) cart.splice(idx, 1);
        renderCart();
    }

    function removeItem(idx) {
        cart.splice(idx, 1);
        renderCart();
    }

    function clearCart() {
        cart = [];
        document.getElementById('diskonInput').value = 0;
        renderCart();
    }

    function updateTotal() {
        const subtotal = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
        const diskon = parseFloat(document.getElementById('diskonInput').value) || 0;
        const total = Math.max(0, subtotal - diskon);
        document.getElementById('cartTotal').textContent = 'Rp ' + formatNumber(total);
    }

    // Process payment
    function processPayment() {
        if (cart.length === 0) return;

        const btn = document.getElementById('btnPay');
        btn.disabled = true;
        btn.textContent = '⏳ Memproses...';

        if (currentMode === 'sale') {
            processSale();
        } else {
            processPurchase();
        }
    }

    function processSale() {
        const data = {
            akun_kas_bank: document.getElementById('akun_kas_bank').value,
            diskon_total: parseFloat(document.getElementById('diskonInput').value) || 0,
            items: cart.map(i => ({ id_barang: i.id_barang, qty: i.qty, harga: i.harga })),
        };

        fetch('{{ route("pos.store.sale") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(data),
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                lastTransactionId = res.id_penjualan;
                lastTransactionType = 'sale';
                document.getElementById('successTitle').textContent = 'Penjualan Berhasil! ✅';
                document.getElementById('successInfo').textContent = `No. Faktur: ${res.no_faktur} | Total: Rp ${formatNumber(res.total)}`;
                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                alert('Error: ' + res.message);
            }
            document.getElementById('btnPay').disabled = false;
            document.getElementById('btnPay').textContent = '💳 BAYAR';
        })
        .catch(err => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            document.getElementById('btnPay').disabled = false;
            document.getElementById('btnPay').textContent = '💳 BAYAR';
        });
    }

    function processPurchase() {
        const pemasok = document.getElementById('id_pemasok').value;
        if (!pemasok) { alert('Pilih pemasok terlebih dahulu!'); document.getElementById('btnPay').disabled = false; document.getElementById('btnPay').textContent = '📥 SIMPAN PEMBELIAN'; return; }

        const data = {
            id_pemasok: pemasok,
            metode_pembayaran: document.getElementById('metode_pembayaran_buy').value,
            akun_kas_bank: document.getElementById('akun_kas_bank').value,
            items: cart.map(i => ({ id_barang: i.id_barang, qty: i.qty, harga_beli: i.harga })),
        };

        fetch('{{ route("pos.store.purchase") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(data),
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                lastTransactionId = res.id_pembelian;
                lastTransactionType = 'purchase';
                document.getElementById('successTitle').textContent = 'Pembelian Berhasil! ✅';
                document.getElementById('successInfo').textContent = `No. Faktur: ${res.no_faktur} | Total: Rp ${formatNumber(res.total)}`;
                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                alert('Error: ' + res.message);
            }
            document.getElementById('btnPay').disabled = false;
            document.getElementById('btnPay').textContent = '📥 SIMPAN PEMBELIAN';
        })
        .catch(err => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            document.getElementById('btnPay').disabled = false;
            document.getElementById('btnPay').textContent = '📥 SIMPAN PEMBELIAN';
        });
    }

    function printReceipt() {
        if (lastTransactionType === 'sale') {
            window.open(`/pos/receipt/${lastTransactionId}`, '_blank');
        } else {
            window.open(`/pos/purchase-receipt/${lastTransactionId}`, '_blank');
        }
    }

    function resetForNext() {
        clearCart();
        document.getElementById('searchInput').focus();
    }

    function formatNumber(n) {
        return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>
@endpush
