<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Booker Portal') - {{ config('app.name', 'Innotech ERP') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --accent-green: #10b981;
            --bg-light: #f8fafc;
        }
        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            padding-bottom: 75px; /* space for mobile bottom nav */
        }
        .portal-navbar {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .nav-pill-item {
            color: #94a3b8;
            transition: all 0.2s ease;
        }
        .nav-pill-item.active {
            color: #2563eb;
            font-weight: 700;
        }
        /* Bottom navigation bar for mobile */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 62px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1040;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            flex: 1;
            padding: 6px 0;
            transition: all 0.15s ease;
        }
        .bottom-nav-item i {
            font-size: 19px;
            margin-bottom: 2px;
        }
        .bottom-nav-item.active {
            color: #2563eb;
            font-weight: 700;
        }
        .bottom-nav-item.cta {
            position: relative;
            top: -12px;
        }
        .bottom-nav-item.cta .cta-bubble {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            border: 3px solid #ffffff;
        }
        .bottom-nav-item.cta .cta-bubble i {
            font-size: 20px;
            margin-bottom: 0;
        }
        .card-stat {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            transition: transform 0.15s ease;
        }
        .card-stat:hover {
            transform: translateY(-2px);
        }
        .select2-container .select2-selection--single {
            height: 42px !important;
            padding: 6px 12px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>
    @yield('css')
</head>
<body>

    <!-- Top Header -->
    <nav class="navbar navbar-dark portal-navbar py-2 px-3 sticky-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a class="navbar-brand fw-bold mb-0 text-white d-flex align-items-center gap-2" href="{{ route('booker.dashboard') }}">
                    <i class="fas fa-clipboard-check text-warning"></i>
                    <span>Order Booker Portal</span>
                </a>
                @if(isset($booker) && $booker)
                    <span class="badge bg-white text-primary font-monospace fw-bold px-2 py-1" style="font-size: 11px;">
                        {{ $booker->code }}
                    </span>
                @endif
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(isset($booker) && $booker)
                    <div class="text-end text-white d-none d-sm-block me-1">
                        <div class="fw-bold small">{{ $booker->name }}</div>
                        <div class="text-white-50" style="font-size: 10px;">{{ $booker->mobile }}</div>
                    </div>
                @endif

                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-outline-light" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-3">
        <div class="container-fluid px-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center gap-2" role="alert">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center gap-2" role="alert">
                    <i class="fas fa-exclamation-triangle fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Fixed Bottom Navigation -->
    <div class="bottom-nav">
        <a href="{{ route('booker.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('booker.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('booker.shops.index') }}" class="bottom-nav-item {{ request()->routeIs('booker.shops.*') ? 'active' : '' }}">
            <i class="fas fa-store"></i>
            <span>Shops</span>
        </a>
        <a href="{{ route('booker.orders.create') }}" class="bottom-nav-item cta">
            <div class="cta-bubble">
                <i class="fas fa-plus"></i>
            </div>
            <span class="mt-1 fw-bold text-primary">Book Order</span>
        </a>
        <a href="{{ route('booker.orders.index') }}" class="bottom-nav-item {{ request()->routeIs('booker.orders.*') && !request()->routeIs('booker.orders.create') ? 'active' : '' }}">
            <i class="fas fa-list-alt"></i>
            <span>Bookings</span>
        </a>
        <a href="{{ route('dashboard') }}" class="bottom-nav-item text-muted">
            <i class="fas fa-th-large"></i>
            <span>ERP</span>
        </a>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    @yield('js')
</body>
</html>
