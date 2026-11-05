@extends('layouts.crm')

@title('POS Kasir - Kopi TKP')

@section('styles')
<style>
    /* POS Layout Styles */
    .pos-container {
        display: flex;
        gap: 1.5rem;
        height: calc(100vh - 150px);
    }
    
    .pos-menu {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
    }
    
    .pos-cart {
        width: 420px;
        background-color: var(--card-coffee-dark);
        border: 1px solid rgba(200, 150, 62, 0.15);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .cart-header {
        padding: 1.2rem;
        border-bottom: 1px solid rgba(200, 150, 62, 0.15);
        background-color: rgba(0, 0, 0, 0.15);
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }
    
    .cart-items {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .cart-item {
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(200, 150, 62, 0.08);
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: border-color 0.2s ease;
    }
    
    .cart-item:hover {
        border-color: rgba(200, 150, 62, 0.25);
    }
    
    .cart-summary {
        padding: 1.2rem;
        border-top: 1px solid rgba(200, 150, 62, 0.15);
        background-color: rgba(0, 0, 0, 0.15);
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    .product-grid-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-grid-card:hover {
        transform: translateY(-3px);
        border-color: var(--accent-gold);
    }

    .category-pill {
        cursor: pointer;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        background-color: rgba(0, 0, 0, 0.2);
        border: 1.5px solid rgba(200, 150, 62, 0.2);
        color: var(--text-muted-coffee);
        transition: all 0.2s ease;
    }

    .category-pill.active, .category-pill:hover {
        background-color: var(--accent-gold);
        border-color: var(--accent-gold);
        color: #fff;
        box-shadow: 0 4px 10px rgba(200, 150, 62, 0.15);
    }

    /* Custom Scrollbar */
    .pos-menu::-webkit-scrollbar, .cart-items::-webkit-scrollbar {
        width: 6px;
    }
    .pos-menu::-webkit-scrollbar-track, .cart-items::-webkit-scrollbar-track {
        background: transparent;
    }
    .pos-menu::-webkit-scrollbar-thumb, .cart-items::-webkit-scrollbar-thumb {
        background-color: rgba(200, 150, 62, 0.15);
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="pos-container">
    
    <!-- Left Panel: Menu Kopi -->
    <div class="pos-menu">
        <!-- Search & Categories -->
        <div class="card-coffee p-3 mb-4">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-warning text-warning"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control form-control-coffee py-2" id="searchMenu" placeholder="Cari kopi/makanan...">
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="d-flex flex-wrap gap-2" id="categoryFilters">
                        <span class="category-pill active" data-category="Semua">Semua Menu</span>
                        <span class="category-pill" data-category="Kopi">Kopi</span>
                        <span class="category-pill" data-category="Non-Kopi">Non-Kopi</span>
                        <span class="category-pill" data-category="Makanan">Makanan</span>
                        <span class="category-pill" data-category="Cemilan">Cemilan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="row g-3" id="productGrid">
            @foreach($produk as $p)
                <div class="col-12 col-sm-6 col-xl-4 product-card-item" data-id="{{ $p->id }}" data-name="{{ $p->nama_produk }}" data-price="{{ $p->harga }}" data-category="{{ $p->kategori }}" data-stock="{{ $p->stok }}">
                    <div class="card-coffee h-100 product-grid-card">
                        <div class="position-relative">
                            @if($p->gambar)
                                <img src="{{ asset($p->gambar) }}" alt="{{ $p->nama_produk }}" class="card-img-top object-fit-cover" style="height: 120px;">
                            @else
                                <div class="card-img-top bg-dark text-warning d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="fa-solid fa-mug-hot fs-2"></i>
                                </div>
                            @endif
                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark text-warning border border-warning font-monospace">
                                Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="p-3">
                            <h6 class="fw-bold text-light mb-1 text-truncate">{{ $p->nama_produk }}</h6>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted-coffee">Stok: {{ $p->stok }}</small>
                                <button class="btn btn-sm btn-gold py-0.5 px-2.5 btn-add-cart" data-id="{{ $p->id }}"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Panel: Cart & CRM Checkout -->
    <div class="pos-cart">
        <div class="cart-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-cart-shopping me-2 text-warning"></i>Keranjang Belanja</h5>
        </div>

        <!-- Cart Items List (JS render) -->
        <div class="cart-items" id="cartContainer">
            <div class="text-center text-muted-coffee py-5" id="cartEmptyMessage">
                <i class="fa-solid fa-mug-saucer fs-2 mb-2 text-warning d-block"></i> Keranjang kosong.<br>Klik produk untuk menambah.
            </div>
        </div>

        <!-- CRM discount & Payment Calculator -->
        <div class="cart-summary">
            <!-- 1. Customer Select (CRM Integration) -->
            <div class="mb-3">
                <label for="selectPelanggan" class="form-label-coffee" style="font-size: 0.85rem;"><i class="fa-solid fa-id-card me-1"></i> Pilih Member CRM (Opsional)</label>
                <select class="form-select form-control-coffee py-1.5" id="selectPelanggan">
                    <option value="" selected>Guest / Umum (Tanpa Member)</option>
                    @foreach($pelanggan as $plg)
                        <option value="{{ $plg->id }}" data-tier="{{ $plg->status_member }}" data-points="{{ $plg->poin }}">
                            {{ $plg->nama }} ({{ $plg->kode_pelanggan }} | {{ $plg->status_member }})
                        </option>
                    @endforeach
                </select>
                
                <!-- Display member benefit details dynamically -->
                <div class="mt-2 p-2 rounded bg-dark border border-warning border-opacity-10 d-none" id="memberDetailsBox">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-light">Level: <span class="badge-member" id="lblMemberTier">Bronze</span></small>
                        <small class="text-info"><i class="fa-solid fa-coins"></i> <span id="lblMemberPoints">0</span> Poin</small>
                    </div>
                    <!-- Points swap option -->
                    <div class="form-check mt-2 pt-1 border-top border-secondary border-opacity-25" id="pointsRedeemCheck">
                        <input class="form-check-input" type="checkbox" id="chkUsePoints">
                        <label class="form-check-label text-warning" for="chkUsePoints" style="font-size: 0.8rem;">
                            Tukar 50 Poin (Potongan Rp 22.000)
                        </label>
                    </div>
                </div>
            </div>

            <!-- 2. Promo Code Code Field -->
            <div class="mb-3">
                <label for="txtPromoCode" class="form-label-coffee" style="font-size: 0.85rem;"><i class="fa-solid fa-tags me-1"></i> Kode Voucher Promo</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-coffee py-1.5" id="txtPromoCode" placeholder="Contoh: TKPAWAL10" style="text-transform: uppercase;">
                    <button class="btn btn-outline-gold py-1.5" type="button" id="btnApplyPromo">Terapkan</button>
                </div>
                <div class="form-text text-success d-none" id="promoSuccessMsg" style="font-size: 0.75rem;">Promo diterapkan!</div>
                <div class="form-text text-danger d-none" id="promoErrorMsg" style="font-size: 0.75rem;">Kode promo tidak valid/min. belanja kurang.</div>
            </div>

            <!-- 3. Totals Display -->
            <div class="border-top border-secondary border-opacity-25 pt-2 mb-3">
                <div class="d-flex justify-content-between text-muted-coffee mb-1" style="font-size: 0.85rem;">
                    <span>Total Harga:</span>
                    <span id="summaryGross">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between text-danger mb-1" style="font-size: 0.85rem;">
                    <span>Potongan Diskon:</span>
                    <span id="summaryDiscount">-Rp 0</span>
                </div>
                <div class="d-flex justify-content-between text-light fw-bold border-top border-secondary border-opacity-10 pt-2 mb-2" style="font-size: 1.1rem;">
                    <span class="text-warning">Total Bayar:</span>
                    <span id="summaryNet">Rp 0</span>
                </div>
                <!-- Points Earned projection -->
                <div class="text-info d-none" id="pointsProjectedMsg" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-coins me-1"></i> Member akan mendapatkan <strong id="valProjectedPoints">0</strong> poin baru.
                </div>
            </div>

            <!-- 4. Payment Calculator -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label-coffee" style="font-size: 0.8rem;">Metode Bayar</label>
                    <select class="form-select form-control-coffee py-1.5" id="selectPayment">
                        <option value="Cash" selected>Cash (Tunai)</option>
                        <option value="QRIS">QRIS (Digital)</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label-coffee" style="font-size: 0.8rem;">Uang Diterima</label>
                    <input type="number" class="form-control form-control-coffee py-1.5 text-end font-monospace" id="numCashTender" placeholder="0">
                </div>
                <div class="col-12 mt-2" id="changeCalculateBox">
                    <div class="d-flex justify-content-between text-info fw-bold bg-dark p-2 rounded border border-info border-opacity-10" style="font-size: 0.9rem;">
                        <span>Kembalian:</span>
                        <span id="lblChangeMoney">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- 5. Submit Button -->
            <button class="btn btn-gold w-100 py-2.5 fw-bold text-uppercase" id="btnCheckoutSubmit">
                <i class="fa-solid fa-cash-register me-2"></i> Proses Checkout
            </button>
        </div>
    </div>
</div>

<!-- QRIS simulated scanner modal -->
<div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content card-coffee">
            <div class="modal-header card-coffee-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-warning" id="qrisModalLabel">
                    <i class="fa-solid fa-qrcode me-2"></i> Pembayaran QRIS Dinamis
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="p-3 bg-white rounded-3 d-inline-block shadow-sm border mb-3">
                    <!-- Dynamic QR code mockup -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=KOPI_TKP_MOCKUP_PAYMENT" alt="QRIS" class="img-fluid" style="width: 200px; height: 200px;">
                </div>
                <h5 class="fw-bold text-light mb-1">Kopi Tempat Kamu Pulang</h5>
                <div class="text-warning fw-bold fs-4 mb-2" id="qrisModalAmount">Rp 0</div>
                <p class="text-muted-coffee" style="font-size: 0.85rem;">Tunjukkan kode QRIS ini ke pelanggan untuk dipindai. Klik tombol di bawah setelah pembayaran terverifikasi lunas di saldo rekening merchant.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-gold w-100" id="btnQrisConfirm">
                    <i class="fa-solid fa-check-circle me-1"></i> Pembayaran Terverifikasi Lunas
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Raw Promotions array from controller to compute coupon discounts in real-time
    const activePromotions = {!! json_encode($promosi) !!};

    $(document).ready(function() {
        let cart = {}; // key: product_id, value: {id, name, price, qty, stock}
        let appliedPromo = null;
        
        // Search Menu Filtering
        $("#searchMenu").on("keyup", function() {
            let val = $(this).val().toLowerCase();
            $("#productGrid .product-card-item").filter(function() {
                $(this).toggle($(this).attr("data-name").toLowerCase().indexOf(val) > -1);
            });
        });

        // Category Filter
        $("#categoryFilters .category-pill").click(function() {
            $("#categoryFilters .category-pill").removeClass("active");
            $(this).addClass("active");
            
            let category = $(this).attr("data-category");
            $("#productGrid .product-card-item").filter(function() {
                if (category === "Semua") {
                    $(this).show();
                } else {
                    $(this).toggle($(this).attr("data-category") === category);
                }
            });
        });

        // Add to Cart Action
        $("#productGrid").on("click", ".btn-add-cart, .product-grid-card", function(e) {
            e.stopPropagation();
            let card = $(this).closest(".product-card-item");
            let id = card.attr("data-id");
            let name = card.attr("data-name");
            let price = parseFloat(card.attr("data-price"));
            let stock = parseInt(card.attr("data-stock"));

            if (stock <= 0) {
                alert("Stok menu sudah habis!");
                return;
            }

            if (cart[id]) {
                if (cart[id].qty >= stock) {
                    alert("Jumlah melebihi stok yang tersedia!");
                    return;
                }
                cart[id].qty++;
            } else {
                cart[id] = { id: id, name: name, price: price, qty: 1, stock: stock };
            }

            renderCart();
        });

        // Cart Actions (Quantity Up, Down, Remove)
        $("#cartContainer").on("click", ".btn-qty-up", function() {
            let id = $(this).attr("data-id");
            if (cart[id].qty >= cart[id].stock) {
                alert("Jumlah melebihi stok yang tersedia!");
                return;
            }
            cart[id].qty++;
            renderCart();
        });

        $("#cartContainer").on("click", ".btn-qty-down", function() {
            let id = $(this).attr("data-id");
            if (cart[id].qty > 1) {
                cart[id].qty--;
            } else {
                delete cart[id];
            }
            renderCart();
        });

        $("#cartContainer").on("click", ".btn-qty-remove", function() {
            let id = $(this).attr("data-id");
            delete cart[id];
            renderCart();
        });

        // CRM: Member Selection Change
        $("#selectPelanggan").change(function() {
            let selected = $(this).find(":selected");
            let id = selected.val();
            
            if (id) {
                let tier = selected.attr("data-tier");
                let points = parseInt(selected.attr("data-points"));
                
                $("#lblMemberTier").text(tier);
                // Assign tier badges styling
                $("#lblMemberTier").removeClass("badge-gold badge-silver badge-bronze");
                if (tier === "Gold") $("#lblMemberTier").addClass("badge-member badge-gold");
                else if (tier === "Silver") $("#lblMemberTier").addClass("badge-member badge-silver");
                else $("#lblMemberTier").addClass("badge-member badge-bronze");

                $("#lblMemberPoints").text(points);

                // Show/hide points redeem check based on points balance
                if (points >= 50) {
                    $("#pointsRedeemCheck").show();
                    $("#chkUsePoints").prop("disabled", false);
                } else {
                    $("#pointsRedeemCheck").hide();
                    $("#chkUsePoints").prop("checked", false).prop("disabled", true);
                }
                
                $("#memberDetailsBox").removeClass("d-none");
            } else {
                $("#memberDetailsBox").addClass("d-none");
                $("#chkUsePoints").prop("checked", false);
            }
            
            recalculateTotals();
        });

        // Point Checkbox Switch
        $("#chkUsePoints").change(function() {
            recalculateTotals();
        });

        // Apply Promotion Code Button
        $("#btnApplyPromo").click(function() {
            let code = $("#txtPromoCode").val().trim().toUpperCase();
            $("#promoSuccessMsg, #promoErrorMsg").addClass("d-none");
            appliedPromo = null;

            if (code === "") {
                recalculateTotals();
                return;
            }

            // Find match
            let gross = getGrossTotal();
            let match = activePromotions.find(p => p.kode_promo === code && gross >= parseFloat(p.minimal_transaksi));

            if (match) {
                appliedPromo = match;
                $("#promoSuccessMsg").removeClass("d-none");
            } else {
                $("#promoErrorMsg").removeClass("d-none");
            }

            recalculateTotals();
        });

        // Payment Mode toggle
        $("#selectPayment").change(function() {
            let payment = $(this).val();
            let net = getNetTotal();

            if (payment === "QRIS") {
                $("#numCashTender").val(net.toFixed(0)).prop("disabled", true);
                $("#changeCalculateBox").hide();
            } else {
                $("#numCashTender").val("").prop("disabled", false);
                $("#changeCalculateBox").show();
            }
            calculateChange();
        });

        $("#numCashTender").on("input", function() {
            calculateChange();
        });

        // Function: Render Cart Items
        function renderCart() {
            let container = $("#cartContainer");
            container.empty();

            let keys = Object.keys(cart);
            if (keys.length === 0) {
                $("#cartEmptyMessage").appendTo(container);
                recalculateTotals();
                return;
            }

            keys.forEach(id => {
                let item = cart[id];
                let itemHtml = `
                    <div class="cart-item">
                        <div class="overflow-hidden me-2" style="max-width: 190px;">
                            <div class="fw-bold text-light text-truncate" style="font-size: 0.9rem;">${item.name}</div>
                            <small class="text-warning font-monospace">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-gold p-1 py-0.5 btn-qty-down" data-id="${item.id}" style="width:24px;">-</button>
                            <span class="mx-2 font-monospace fw-bold text-light">${item.qty}</span>
                            <button class="btn btn-sm btn-outline-gold p-1 py-0.5 btn-qty-up" data-id="${item.id}" style="width:24px;">+</button>
                            <button class="btn btn-sm btn-outline-danger p-1 py-0.5 ms-3 btn-qty-remove" data-id="${item.id}"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                `;
                container.append(itemHtml);
            });

            recalculateTotals();
        }

        // Calculation: Gross Total
        function getGrossTotal() {
            let gross = 0;
            Object.keys(cart).forEach(id => {
                gross += (cart[id].price * cart[id].qty);
            });
            return gross;
        }

        // Calculation: Net Total
        function getNetTotal() {
            let gross = getGrossTotal();
            let discount = calculateDiscountValue(gross);
            return Math.max(0, gross - discount);
        }

        // Calculation: Discount Deductions
        function calculateDiscountValue(gross) {
            let discount = 0;

            // 1. Member Tier Discount
            let selected = $("#selectPelanggan").find(":selected");
            if (selected.val()) {
                let tier = selected.attr("data-tier");
                let memberDiscountPersen = 0;
                if (tier === "Gold") memberDiscountPersen = 10;
                else if (tier === "Silver") memberDiscountPersen = 5;

                if (memberDiscountPersen > 0) {
                    discount += (gross * (memberDiscountPersen / 100));
                }
            }

            // 2. Applied Promotion code
            if (appliedPromo) {
                if (appliedPromo.diskon_persen > 0) {
                    discount += (gross * (parseFloat(appliedPromo.diskon_persen) / 100));
                }
                if (appliedPromo.diskon_nominal > 0) {
                    discount += parseFloat(appliedPromo.diskon_nominal);
                }
            }

            // 3. Points Swap discount (50 points = Rp 22.000)
            if ($("#chkUsePoints").is(":checked") && selected.val()) {
                let points = parseInt(selected.attr("data-points"));
                let maxPossibleRedeems = Math.floor(points / 50);
                discount += (maxPossibleRedeems * 22000.00);
            }

            return discount;
        }

        // Calculation: Recalculate Totals view
        function recalculateTotals() {
            let gross = getGrossTotal();
            let discount = calculateDiscountValue(gross);
            let net = getNetTotal();

            $("#summaryGross").text("Rp " + new Intl.NumberFormat('id-ID').format(gross));
            $("#summaryDiscount").text("-Rp " + new Intl.NumberFormat('id-ID').format(discount));
            $("#summaryNet").text("Rp " + new Intl.NumberFormat('id-ID').format(net));

            // Points earned calculation
            let selected = $("#selectPelanggan").find(":selected");
            if (selected.val() && net > 0) {
                let pointsEarned = Math.floor(net / 10000);
                $("#valProjectedPoints").text(pointsEarned);
                $("#pointsProjectedMsg").removeClass("d-none");
            } else {
                $("#pointsProjectedMsg").addClass("d-none");
            }

            // Update cash tender default for QRIS payments
            let payment = $("#selectPayment").val();
            if (payment === "QRIS") {
                $("#numCashTender").val(net.toFixed(0));
            }
            calculateChange();
        }

        // Calculation: Change Due
        function calculateChange() {
            let net = getNetTotal();
            let tender = parseFloat($("#numCashTender").val()) || 0;
            let change = Math.max(0, tender - net);
            
            $("#lblChangeMoney").text("Rp " + new Intl.NumberFormat('id-ID').format(change));
        }

        // Checkout Validation & Submit Trigger
        $("#btnCheckoutSubmit").click(function() {
            let keys = Object.keys(cart);
            if (keys.length === 0) {
                alert("Keranjang belanja kosong! Tambahkan minimal 1 menu kopi.");
                return;
            }

            let net = getNetTotal();
            let payment = $("#selectPayment").val();
            let tender = parseFloat($("#numCashTender").val()) || 0;

            if (tender < net) {
                alert("Uang yang diterima kurang dari total tagihan!");
                return;
            }

            if (payment === "QRIS") {
                // Trigger simulated QR code popup
                $("#qrisModalAmount").text("Rp " + new Intl.NumberFormat('id-ID').format(net));
                let myModal = new bootstrap.Modal(document.getElementById('qrisModal'));
                myModal.show();
            } else {
                submitOrderToServer();
            }
        });

        // QRIS modal scanner payment confirm
        $("#btnQrisConfirm").click(function() {
            $("#qrisModal").modal("hide");
            submitOrderToServer();
        });

        // AJAX Request: Store transaction in backend DB
        function submitOrderToServer() {
            let keys = Object.keys(cart);
            let cartData = keys.map(id => {
                return {
                    produk_id: cart[id].id,
                    qty: cart[id].qty
                };
            });

            let selected = $("#selectPelanggan").find(":selected");
            let pelangganId = selected.val() || null;
            let usePoints = $("#chkUsePoints").is(":checked") ? 50 : 0; // standard redeem 50 points

            let payload = {
                cart: cartData,
                pelanggan_id: pelangganId,
                kode_promo: appliedPromo ? appliedPromo.kode_promo : null,
                poin_digunakan: usePoints,
                metode_pembayaran: $("#selectPayment").val(),
                total_bayar: parseFloat($("#numCashTender").val()) || 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Disable button during ajax loader
            $("#btnCheckoutSubmit").prop("disabled", true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Memproses...');

            $.ajax({
                url: "{{ route('barista.transaksi.store') }}",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert("Terjadi kesalahan: " + response.message);
                        $("#btnCheckoutSubmit").prop("disabled", false).html('<i class="fa-solid fa-cash-register me-2"></i> Proses Checkout');
                    }
                },
                error: function(xhr) {
                    let errMsg = "Terjadi kegagalan memproses checkout POS.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    alert("Error: " + errMsg);
                    $("#btnCheckoutSubmit").prop("disabled", false).html('<i class="fa-solid fa-cash-register me-2"></i> Proses Checkout');
                }
            });
        }
    });
</script>
@endsection
