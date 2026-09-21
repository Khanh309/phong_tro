<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cổng Khách Thuê Trọ') - NhaTroPro</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0284c7;
            --primary-dark: #0369a1;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #0284c7;
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
            font-size: 1.2rem;
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
            margin-bottom: 4px;
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
            cursor: pointer;
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

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 16px 20px;
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
        }

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

        @media print {
            .sidebar, .top-navbar, .no-print {
                display: none !important;
            }
            .main-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .content-area {
                padding: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- TENANT SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-person-badge-fill text-info fs-4"></i>
            <div>
                <div class="lh-1">NhaTroPro</div>
                <span class="badge bg-info text-dark" style="font-size: 0.65rem;">CỔNG KHÁCH THUÊ</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <div class="sidebar-header">Bảng Điều Khiển</div>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}" class="sidebar-link {{ request()->routeIs('portal.index') && !request()->has('tab') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Tổng quan phòng</span>
                </a>
            </li>

            <div class="sidebar-header">Hóa Đơn & Tiền Phòng</div>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}#tab-pending" onclick="switchTab('tab-pending')" class="sidebar-link">
                    <i class="bi bi-clock-history text-warning"></i>
                    <span>Đang chờ nộp</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}#tab-overdue" onclick="switchTab('tab-overdue')" class="sidebar-link text-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <span>Quá hạn nộp</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}#tab-paid" onclick="switchTab('tab-paid')" class="sidebar-link">
                    <i class="bi bi-check2-circle text-success"></i>
                    <span>Lịch sử đã đóng</span>
                </a>
            </li>

            <div class="sidebar-header">Dịch Vụ & Hỗ Trợ</div>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}#tab-fees" onclick="switchTab('tab-fees')" class="sidebar-link">
                    <i class="bi bi-tags-fill text-warning"></i>
                    <span>Biểu phí & Dịch vụ</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}#tab-maintenance" onclick="switchTab('tab-maintenance')" class="sidebar-link">
                    <i class="bi bi-tools text-primary"></i>
                    <span>Báo hỏng đồ đạc</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('portal.index') }}#tab-feedback" onclick="switchTab('tab-feedback')" class="sidebar-link">
                    <i class="bi bi-chat-left-dots-fill text-info"></i>
                    <span>Khiếu nại / Góp ý</span>
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
                <div>
                    @if(isset($room))
                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                            <span>Phòng {{ $room->room_number }}</span>
                            <span class="badge bg-primary-subtle text-primary font-monospace">{{ $room->property->name ?? '' }}</span>
                        </div>
                        <div class="text-muted small" style="font-size: 0.78rem;">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $room->property->address ?? '' }}
                        </div>
                    @else
                        <span class="fw-bold text-dark">Cổng Tra Cứu Hóa Đơn Khách Thuê</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(isset($tenant))
                    <div class="d-none d-md-flex align-items-center gap-2 me-2 pe-3 border-end">
                        <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold small lh-1 text-dark">{{ $tenant->name }}</div>
                            <span class="text-muted" style="font-size: 0.72rem;">{{ $tenant->phone }}</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('portal.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Đổi tài khoản khách thuê khác">
                        <i class="bi bi-arrow-left-right me-1"></i> Đổi Khách
                    </button>
                </form>

                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-shield-lock me-1"></i> Trang Quản Trị
                </a>
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

        function switchTab(tabId) {
            var tabBtn = document.querySelector('button[data-bs-target="#' + tabId + '"]');
            if (tabBtn) {
                var tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }

        // Tự động active tab theo hash URL
        document.addEventListener('DOMContentLoaded', function() {
            var hash = window.location.hash;
            if (hash) {
                switchTab(hash.replace('#', ''));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
