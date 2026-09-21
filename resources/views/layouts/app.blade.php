<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý Nhà Trọ') - NhaTroPro</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #2563eb;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --body-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        }

        .sidebar-brand {
            padding: 20px 24px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 15px 12px;
            list-style: none;
            margin: 0;
        }

        .sidebar-header {
            padding: 12px 14px 6px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
        }

        .sidebar-item {
            margin-bottom: 3px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .sidebar-link.active {
            color: var(--sidebar-text-active);
            background-color: var(--sidebar-active);
            font-weight: 600;
        }

        .sidebar-link i {
            font-size: 1.15rem;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 28px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-area {
            padding: 28px;
            flex-grow: 1;
        }

        /* Card enhancements */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 16px 20px;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        /* Custom utilities */
        .badge-subtle-success { background-color: #dcfce7; color: #15803d; }
        .badge-subtle-danger { background-color: #fee2e2; color: #b91c1c; }
        .badge-subtle-warning { background-color: #fef3c7; color: #b45309; }
        .badge-subtle-info { background-color: #e0f2fe; color: #0369a1; }
        .badge-subtle-primary { background-color: #dbeafe; color: #1d4ed8; }

        @media (max-width: 991px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-buildings-fill text-primary"></i>
            <span>NhaTroPro</span>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Tổng quan</span>
                </a>
            </li>

            <div class="sidebar-header">Nhà trọ & Tòa nhà</div>
            <li class="sidebar-item">
                <a href="{{ route('properties.index') }}" class="sidebar-link {{ request()->routeIs('properties.index', 'properties.show') ? 'active' : '' }}">
                    <i class="bi bi-buildings-fill text-primary"></i>
                    <span>{{ auth()->check() && auth()->user()->isAdmin() ? 'Chuỗi Nhà trọ / Tòa nhà' : 'Thông tin Nhà trọ của tôi' }}</span>
                </a>
            </li>
            @if(auth()->check() && auth()->user()->isAdmin())
                <li class="sidebar-item">
                    <a href="{{ route('properties.create') }}" class="sidebar-link text-primary fw-bold {{ request()->routeIs('properties.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle-fill text-primary"></i>
                        <span>+ Thêm Nhà Trọ Mới</span>
                    </a>
                </li>
            @else
                <li class="sidebar-item">
                    <span class="sidebar-link text-muted small fst-italic" style="cursor: default;" title="Chỉ Admin mới có quyền thêm/sửa cơ sở">
                        <i class="bi bi-lock text-muted"></i>
                        <span>Thêm nhà trọ (Chỉ Admin)</span>
                    </span>
                </li>
            @endif

            <div class="sidebar-header">Phòng trọ & Dịch vụ</div>
            <li class="sidebar-item">
                <a href="{{ route('rooms.index') }}" class="sidebar-link {{ request()->routeIs('rooms.index') && request('view') !== 'fees' ? 'active' : '' }}">
                    <i class="bi bi-door-open-fill"></i>
                    <span>Sơ đồ Phòng trọ</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('rooms.index', ['view' => 'fees']) }}" class="sidebar-link {{ request('view') === 'fees' ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff text-warning"></i>
                    <span>Biểu phí dịch vụ các phòng</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('rooms.vacant_finder') }}" class="sidebar-link {{ request()->routeIs('rooms.vacant_finder') ? 'active' : '' }}">
                    <i class="bi bi-search"></i>
                    <span>Tìm phòng trống</span>
                </a>
            </li>

            <div class="sidebar-header">Khách & Hợp đồng</div>
            <li class="sidebar-item">
                <a href="{{ route('tenants.index') }}" class="sidebar-link {{ request()->routeIs('tenants.index', 'tenants.create', 'tenants.show', 'tenants.edit') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Hồ sơ Khách thuê</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('tenants.police_report') }}" class="sidebar-link {{ request()->routeIs('tenants.police_report') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i>
                    <span>Khai báo Tạm trú</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('contracts.index') }}" class="sidebar-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>Hợp đồng & Cọc</span>
                </a>
            </li>

            <div class="sidebar-header">Thu Tiền & Điện Nước</div>
            <li class="sidebar-item">
                <a href="{{ route('invoices.bulk_create') }}" class="sidebar-link {{ request()->routeIs('invoices.bulk_create') ? 'active' : '' }}">
                    <i class="bi bi-lightning-charge-fill text-warning"></i>
                    <span>Chốt điện nước nhanh</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('invoices.index') }}" class="sidebar-link {{ request()->routeIs('invoices.index', 'invoices.show', 'invoices.create') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Hóa đơn phòng</span>
                </a>
            </li>

            @if(auth()->check() && auth()->user()->isAdmin())
                <div class="sidebar-header">Chi Phí & Tài Chính (Chủ Trọ)</div>
                <li class="sidebar-item">
                    <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack text-danger"></i>
                        <span>Chi phí trả Nhà nước</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('reports.financial') }}" class="sidebar-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                        <span>Báo cáo Lợi nhuận & Thất thoát</span>
                    </a>
                </li>
            @endif

            <div class="sidebar-header">Vận hành & Khách xem</div>
            <li class="sidebar-item">
                <a href="{{ route('maintenance.index') }}" class="sidebar-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i>
                    <span>Báo hỏng & Sửa chữa</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}" target="_blank" class="sidebar-link text-info fw-semibold">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Cổng Khách Thuê Trọ</span>
                    <span class="badge bg-info text-dark ms-auto small">Mới</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-wrapper">
        <!-- TOP NAVBAR -->
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="text-muted small">Hệ thống Quản lý Chuỗi Nhà trọ & Căn hộ cho thuê</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('properties.create') }}" class="btn btn-sm btn-outline-primary fw-bold shadow-sm">
                        <i class="bi bi-building-add me-1"></i> + Thêm Nhà Trọ
                    </a>
                @endif
                <a href="{{ route('portal.index') }}" target="_blank" class="btn btn-sm btn-info text-dark fw-bold shadow-sm">
                    <i class="bi bi-person-badge-fill me-1"></i> Cổng Khách Thuê
                </a>
                <a href="{{ route('invoices.bulk_create') }}" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Chốt điện nước
                </a>
                <a href="{{ route('contracts.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> Khách mới
                </a>

                <!-- THÔNG TIN TÀI KHOẢN ĐANG ĐĂNG NHẬP & PHÂN QUYỀN -->
                @auth
                    <div class="dropdown ms-2 ps-2 border-start">
                        <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="text-start d-none d-sm-block">
                                <div class="fw-bold small lh-1">{{ auth()->user()->name }}</div>
                                <span class="badge {{ auth()->user()->isAdmin() ? 'bg-primary' : 'bg-success' }}" style="font-size: 0.65rem;">
                                    {{ auth()->user()->isAdmin() ? '👑 Admin Tổng Thể' : '👔 Quản Lý: ' . (auth()->user()->property->name ?? 'Cơ sở') }}
                                </span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="dropdown-header small">
                                Email: <b>{{ auth()->user()->email }}</b>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger small fw-semibold">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </nav>

        <!-- CONTENT AREA -->
        <main class="content-area">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
