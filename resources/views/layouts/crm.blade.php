<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Kopi Tempat Kamu Pulang (TKP) CRM</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Chart.js (Local offline first, CDN fallback) -->
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        if (typeof Chart === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>');
        }
    </script>

    <!-- Custom CSS Design System -->
    <style>
        :root {
            --primary-coffee: #3A2420;
            --primary-coffee-light: #563732;
            --accent-gold: #D4A047;
            --accent-gold-hover: #EAB965;
            --bg-dark-espresso: #221614;
            --card-coffee-dark: #2D1E1B;
            --card-coffee-hover: #382522;
            --cream-soft: #FAF4ED;
            --text-muted-coffee: #C7B3AE;
            --text-light: #FAF5F2;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark-espresso);
            color: var(--text-light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            height: 100vh;
            width: 280px;
            background-color: var(--primary-coffee);
            border-right: 1px solid rgba(212, 160, 71, 0.2);
            transition: all 0.3s ease;
            position: fixed;
            z-index: 1000;
            overflow-y: auto;
        }

        /* Custom scrollbar for sidebar */
        #sidebar-wrapper::-webkit-scrollbar {
            width: 6px;
        }
        #sidebar-wrapper::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        #sidebar-wrapper::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 3px;
        }
        #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--accent-gold-hover);
        }

        .sidebar-brand {
            padding: 1.5rem 1.2rem;
            border-bottom: 1px solid rgba(212, 160, 71, 0.18);
        }

        .sidebar-brand h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--accent-gold);
            margin: 0;
            font-size: 1.45rem;
        }

        .sidebar-brand p {
            margin: 0;
            font-size: 0.75rem;
            color: var(--text-muted-coffee);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .sidebar-user {
            padding: 1.2rem;
            background: rgba(0, 0, 0, 0.18);
            border-bottom: 1px solid rgba(212, 160, 71, 0.18);
        }

        .sidebar-nav {
            padding: 1rem 0.8rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-nav-item {
            margin-bottom: 0.4rem;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-muted-coffee);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar-nav-link i, .sidebar-nav-link img {
            width: 24px;
            margin-right: 12px;
            font-size: 1.15rem;
            text-align: center;
        }

        .sidebar-nav-link:hover {
            background-color: rgba(212, 160, 71, 0.14);
            color: var(--accent-gold-hover);
        }

        .sidebar-nav-link.active {
            background: linear-gradient(135deg, var(--accent-gold), var(--primary-coffee-light));
            color: #fff;
            box-shadow: 0 4px 12px rgba(212, 160, 71, 0.28);
        }

        /* Page Content Wrapper */
        #page-content-wrapper {
            margin-left: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .top-navbar {
            background-color: var(--card-coffee-dark);
            border-bottom: 1px solid rgba(212, 160, 71, 0.2);
            padding: 0.85rem 1.5rem;
        }

        .main-content {
            padding: 2rem;
            flex-grow: 1;
            background: radial-gradient(circle at top right, var(--primary-coffee-light) 0%, var(--bg-dark-espresso) 75%);
        }

        /* Cards Styling */
        .card-coffee {
            background-color: var(--card-coffee-dark);
            border: 1px solid rgba(212, 160, 71, 0.2);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            color: var(--text-light);
        }

        .card-coffee:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(212, 160, 71, 0.12);
            border-color: rgba(212, 160, 71, 0.35);
        }

        .card-coffee-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(212, 160, 71, 0.16);
            background-color: rgba(255, 255, 255, 0.03);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .card-coffee-body {
            padding: 1.5rem;
        }

        /* Buttons Styling */
        .btn-gold {
            background: linear-gradient(135deg, var(--accent-gold), #BF8B34);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(212, 160, 71, 0.22);
        }

        .btn-gold:hover, .btn-gold:focus {
            background: linear-gradient(135deg, var(--accent-gold-hover), var(--accent-gold));
            color: #fff;
            box-shadow: 0 6px 14px rgba(212, 160, 71, 0.38);
            transform: translateY(-1px);
        }

        .btn-outline-gold {
            background: transparent;
            color: var(--accent-gold);
            border: 1.5px solid var(--accent-gold);
            border-radius: 8px;
            font-weight: 600;
            padding: 0.55rem 1.2rem;
            transition: all 0.2s ease;
        }

        .btn-outline-gold:hover {
            background-color: var(--accent-gold);
            color: #fff;
            box-shadow: 0 4px 12px rgba(212, 160, 71, 0.22);
        }

        /* Accessible local table controls and pagination */
        .table-coffee {
            background-color: var(--card-coffee-dark) !important;
            border-color: rgba(212, 160, 71, 0.2) !important;
            color: var(--text-light) !important;
        }

        .table-coffee th {
            background-color: rgba(30, 18, 16, 0.75) !important;
            color: var(--accent-gold) !important;
            border-bottom: 2px solid rgba(212, 160, 71, 0.3) !important;
            padding: 1rem 0.75rem !important;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        .table-coffee td {
            background-color: transparent !important;
            border-bottom: 1px solid rgba(212, 160, 71, 0.12) !important;
            padding: 0.95rem 0.75rem !important;
            color: var(--text-light);
            vertical-align: middle;
        }

        .table-coffee tr:hover td {
            background-color: rgba(212, 160, 71, 0.08) !important;
        }

        .client-table-wrapper {
            width: 100%;
        }

        .client-table-toolbar,
        .client-table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            color: var(--text-muted-coffee);
            margin: 0.75rem 0;
        }

        .client-table-search-label {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: var(--text-light);
        }

        .client-table-search,
        .client-table-page-size {
            min-height: 2.4rem;
            background: #211512;
            border: 1px solid rgba(212, 160, 71, 0.5);
            border-radius: 0.5rem;
            color: var(--text-light);
            padding: 0.4rem 0.7rem;
        }

        .client-table-search::placeholder {
            color: #C7B3AE;
            opacity: 1;
        }

        .client-table-search:focus,
        .client-table-page-size:focus {
            outline: 2px solid var(--accent-gold-hover);
            outline-offset: 1px;
        }

        .client-table-navigation {
            display: flex;
            gap: 0.5rem;
        }

        .client-table-page-button {
            min-height: 2.4rem;
            border: 1px solid rgba(212, 160, 71, 0.55);
            border-radius: 0.45rem;
            background: #2D1E1B;
            color: #FAF5F2;
            padding: 0.35rem 0.75rem;
        }

        .client-table-page-button:not(:disabled):hover {
            background: var(--accent-gold);
            color: #211512;
        }

        .client-table-page-button:disabled {
            color: #C7B3AE;
            opacity: 0.65;
            cursor: not-allowed;
        }

        .client-table-sortable {
            cursor: pointer;
        }

        .client-table-sortable:focus-visible {
            outline: 2px solid var(--accent-gold-hover);
            outline-offset: -3px;
        }

        .client-table-empty {
            color: var(--text-muted-coffee) !important;
            text-align: center;
            padding: 1.5rem !important;
        }

        @media (max-width: 767.98px) {
            .client-table-search-label {
                flex: 1 1 100%;
            }

            .client-table-search {
                flex: 1;
                min-width: 0;
            }

            .client-table-footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        /* Form Inputs custom styling */
        .form-control-coffee {
            background-color: rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(212, 160, 71, 0.25) !important;
            color: var(--text-light) !important;
            border-radius: 8px;
            padding: 0.65rem 1rem;
        }

        .form-control-coffee:focus {
            background-color: rgba(0, 0, 0, 0.35) !important;
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 0 0.25rem rgba(212, 160, 71, 0.2) !important;
            color: var(--text-light) !important;
        }

        .form-label-coffee {
            color: var(--text-muted-coffee);
            font-weight: 500;
            margin-bottom: 0.4rem;
        }

        /* Badge Member Styling */
        .badge-member {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-gold {
            background: linear-gradient(135deg, #FFE082, var(--accent-gold));
            color: #3b2806;
            box-shadow: 0 2px 8px rgba(212, 160, 71, 0.25);
        }
        .badge-silver {
            background: linear-gradient(135deg, #E0E0E0, #9E9E9E);
            color: #212121;
        }
        .badge-bronze {
            background: linear-gradient(135deg, #D7CCC8, #8D6E63);
            color: #3e2723;
        }

        /* Toast / Alert overrides */
        .alert-coffee {
            background-color: rgba(212, 160, 71, 0.18);
            border: 1px solid var(--accent-gold);
            color: var(--accent-gold-hover);
            border-radius: 10px;
        }

        /* Footer styling */
        .footer {
            background-color: var(--card-coffee-dark);
            border-top: 1px solid rgba(200, 150, 62, 0.1);
            padding: 1rem 0;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted-coffee);
        }

        /* Toggle Button Sidebar for Mobile */
        .menu-toggle-btn {
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: -280px;
            }
            #page-content-wrapper {
                margin-left: 0;
                width: 100%;
            }
            body.sidebar-toggled #sidebar-wrapper {
                margin-left: 0;
            }
            body.sidebar-toggled #page-content-wrapper {
                margin-left: 280px;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="">

    <!-- Sidebar Wrapper -->
    <div id="sidebar-wrapper">
        <div class="sidebar-brand">
            <h3>Kopi TKP</h3>
            <p>Tempat Kamu Pulang</p>
        </div>
        
        <!-- User Badge -->
        <div class="sidebar-user d-flex align-items-center">
            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center border border-warning" style="width: 48px; height: 48px; min-width: 48px;">
                <i class="fa-solid fa-user-ninja text-warning fs-5"></i>
            </div>
            <div class="ms-3 overflow-hidden">
                <div class="fw-bold text-truncate" style="font-size: 0.95rem;">{{ Auth::user()->name }}</div>
                <div class="text-warning text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                    @if(Auth::user()->role === 'owner')
                        <i class="fa-solid fa-crown me-1"></i> Owner
                    @elseif(Auth::user()->role === 'barista')
                        <i class="fa-solid fa-cash-register me-1"></i> Kasir
                    @else
                        <i class="fa-solid fa-user me-1"></i> Pelanggan
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <ul class="sidebar-nav">
            @if(Auth::user()->role === 'owner')
                <!-- OWNER MENUS -->
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.dashboard') }}" class="sidebar-nav-link {{ Route::is('owner.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.produk.index') }}" class="sidebar-nav-link {{ Route::is('owner.produk.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-mug-hot"></i> Menu Kopi (Produk)
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.pelanggan.index') }}" class="sidebar-nav-link {{ Route::is('owner.pelanggan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i> Kelola Pelanggan
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.promosi.index') }}" class="sidebar-nav-link {{ Route::is('owner.promosi.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i> Program Promosi
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.pengguna.index') }}" class="sidebar-nav-link {{ Route::is('owner.pengguna.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-tie"></i> Kelola Pengguna
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.transaksi.index') }}" class="sidebar-nav-link {{ Route::is('owner.transaksi.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i> Kelola Transaksi
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.laporan.index') }}" class="sidebar-nav-link {{ Route::is('owner.laporan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Laporan Penjualan
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('owner.loyalitas.index') }}" class="sidebar-nav-link {{ Route::is('owner.loyalitas.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-award"></i> Loyalitas Pelanggan
                    </a>
                </li>
            @endif

            @if(Auth::user()->role === 'barista')
                <!-- BARISTA MENUS -->
                <li class="sidebar-nav-item">
                    <a href="{{ route('barista.dashboard') }}" class="sidebar-nav-link {{ Route::is('barista.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house-laptop"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('barista.transaksi.create') }}" class="sidebar-nav-link {{ Route::is('barista.transaksi.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-cash-register"></i> POS Kasir (Checkout)
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('barista.produk') }}" class="sidebar-nav-link {{ Route::is('barista.produk') ? 'active' : '' }}">
                        <i class="fa-solid fa-mug-hot"></i> Daftar Menu
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('barista.transaksi.index') }}" class="sidebar-nav-link {{ Route::is('barista.transaksi.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi
                    </a>
                </li>
            @endif

            @if(Auth::user()->role === 'pelanggan')
                <!-- CUSTOMER MENUS -->
                <li class="sidebar-nav-item">
                    <a href="{{ route('pelanggan.dashboard') }}" class="sidebar-nav-link {{ Route::is('pelanggan.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-user"></i> Dashboard Member
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('pelanggan.produk') }}" class="sidebar-nav-link {{ Route::is('pelanggan.produk') ? 'active' : '' }}">
                        <i class="fa-solid fa-mug-saucer"></i> Menu Kopi TKP
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('pelanggan.promosi') }}" class="sidebar-nav-link {{ Route::is('pelanggan.promosi') ? 'active' : '' }}">
                        <i class="fa-solid fa-gift"></i> Voucher Reward Poin
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('pelanggan.transaksi.index') }}" class="sidebar-nav-link {{ Route::is('pelanggan.transaksi.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i> Riwayat Transaksi
                    </a>
                </li>
            @endif

            <li class="sidebar-nav-item mt-4 pt-4 border-top" style="border-color: rgba(200, 150, 62, 0.1) !important;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="sidebar-nav-link text-danger"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fa-solid fa-power-off text-danger"></i> Keluar
                    </a>
                </form>
            </li>
        </ul>
    </div>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar navbar-dark">
            <div class="container-fluid p-0">
                <button class="menu-toggle-btn d-lg-none" id="menu-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                
                <h5 class="m-0 ms-3 fw-bold text-uppercase d-none d-sm-block" style="color: var(--accent-gold); letter-spacing: 1px;">
                    Portal CRM & Loyalitas - Kopi TKP
                </h5>
                
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-muted-coffee me-3 d-none d-md-block" style="font-size: 0.9rem;">
                        <i class="fa-regular fa-calendar-check me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
                    </span>
                    
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-gold px-3">
                        <i class="fa-regular fa-user-circle me-1"></i> Edit Akun
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Alert Session Messages -->
            @if(session('success'))
                <div class="alert alert-coffee alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 bg-danger bg-opacity-10 text-danger border-danger border-opacity-25" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <span>&copy; 2026 Kopi Tempat Kamu Pulang (TKP) - Tugas Akhir Sistem Informasi. All Rights Reserved.</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Local table search, sorting, and pagination (no external plugin required) -->
    <script type="module" src="{{ asset('js/table-pagination.js') }}"></script>

    @yield('scripts')

    <script>
        $(document).ready(function() {
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("body").toggleClass("sidebar-toggled");
            });
            
            // Auto close sidebar on clicking content area in mobile view
            $(".main-content").click(function() {
                if ($("body").hasClass("sidebar-toggled")) {
                    $("body").removeClass("sidebar-toggled");
                }
            });
        });
    </script>

</body>
</html>
