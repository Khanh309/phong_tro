<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NhaTroPro - Tìm Phòng Trọ Tiện Nghi, Giá Tốt & An Ninh</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-success: #16a34a;
            --brand-dark: #0f172a;
            --card-border: #e2e8f0;
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .site-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--brand-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Hero Banner */
        .hero-wrap {
            background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 40px 0 45px;
        }

        /* Search Pill Bar */
        .search-pill-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            padding: 12px 16px;
        }

        .quick-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            transition: all 0.15s ease;
        }

        .quick-filter-chip:hover, .quick-filter-chip.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        /* Room Card */
        .room-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .room-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            border-color: #94a3b8;
        }

        .room-thumb {
            height: 140px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.4);
        }

        .room-thumb-icon {
            font-size: 3.5rem;
        }

        .thumb-badge-left {
            position: absolute;
            top: 12px;
            left: 12px;
        }

        .thumb-badge-right {
            position: absolute;
            top: 12px;
            right: 12px;
        }

        .price-text {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--brand-success);
        }

        .chip-amenity {
            font-size: 0.75rem;
            background: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        /* Features */
        .benefit-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            height: 100%;
        }

        /* Footer */
        footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- TOP NAVBAR -->
    <header class="site-header py-2">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- LOGO -->
                <a href="{{ route('home') }}" class="brand-logo">
                    <span class="bg-primary text-white rounded-3 p-2 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                        <i class="bi bi-buildings-fill fs-5"></i>
                    </span>
                    <span>NhaTro<span class="text-primary">Pro</span></span>
                </a>

                <!-- QUICK NAV (DESKTOP) -->
                <nav class="d-none d-md-flex align-items-center gap-4">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none fw-semibold">
                        <i class="bi bi-house-door me-1 text-primary"></i> Trang Chủ
                    </a>
                    <a href="#danh-sach-phong" class="text-secondary text-decoration-none">
                        <i class="bi bi-door-open me-1"></i> Phòng Trống ({{ $totalVacantCount }})
                    </a>
                    <a href="#tieu-chuan" class="text-secondary text-decoration-none">
                        <i class="bi bi-shield-check me-1"></i> Tiêu Chuẩn Phòng
                    </a>
                    <a href="{{ route('portal.index') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-receipt me-1"></i> Cổng Khách Thuê
                    </a>
                </nav>

                <!-- ACTIONS (HOTLINE + LOGIN) -->
                <div class="d-flex align-items-center gap-2">
                    <a href="tel:0988888888" class="btn btn-outline-success btn-sm d-none d-lg-inline-flex align-items-center gap-1 rounded-pill px-3">
                        <i class="bi bi-telephone-fill"></i>
                        <span>Hotline: <b>0988.888.888</b></span>
                    </a>

                    @auth
                        @if(auth()->user()->isTenant())
                            <a href="{{ route('portal.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                <i class="bi bi-speedometer2 me-1"></i> Quản Trị
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Đăng Nhập</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- HERO & SEARCH BAR -->
    <section class="hero-wrap">
        <div class="container text-center">
            <h1 class="fw-bold text-dark mb-2 fs-2">
                Tìm Thuê Phòng Trọ Tiện Nghi & Giá Tốt
            </h1>
            <p class="text-muted mb-4 small">
                Chuỗi phòng trọ cao cấp: Khóa vân tay bảo mật, an ninh 24/7, giờ giấc tự do, thanh toán quét mã VietQR.
            </p>

            <!-- THANH TÌM KIẾM GỌN GÀNG -->
            <div class="search-pill-card mx-auto" style="max-width: 960px;">
                <form method="GET" action="{{ route('home') }}#danh-sach-phong" class="row g-2 align-items-center text-start">
                    <!-- Cơ sở / Tòa nhà -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Tòa nhà / Khu vực:
                        </label>
                        <select name="property_id" class="form-select form-select-sm border-secondary-subtle">
                            <option value="">🏢 Tất cả cơ sở ({{ $properties->count() }} tòa)</option>
                            @foreach($properties as $prop)
                                <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>
                                    {{ $prop->name }} ({{ $prop->available_rooms_count }} phòng)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Khoảng giá -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-cash-stack text-success me-1"></i> Mức giá:
                        </label>
                        <select name="price_range" class="form-select form-select-sm border-secondary-subtle">
                            <option value="">💰 Tất cả mức giá</option>
                            <option value="under_3m" {{ $priceRange === 'under_3m' ? 'selected' : '' }}>Dưới 3 triệu</option>
                            <option value="3m_5m" {{ $priceRange === '3m_5m' ? 'selected' : '' }}>Từ 3 - 5 triệu</option>
                            <option value="above_5m" {{ $priceRange === 'above_5m' ? 'selected' : '' }}>Trên 5 triệu</option>
                        </select>
                    </div>

                    <!-- Diện tích -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="bi bi-arrows-angle-expand text-info me-1"></i> Diện tích:
                        </label>
                        <select name="min_area" class="form-select form-select-sm border-secondary-subtle">
                            <option value="">📐 Tất cả diện tích</option>
                            <option value="20" {{ $minArea == '20' ? 'selected' : '' }}>Từ 20 m² trở lên</option>
                            <option value="25" {{ $minArea == '25' ? 'selected' : '' }}>Từ 25 m² trở lên</option>
                            <option value="30" {{ $minArea == '30' ? 'selected' : '' }}>Từ 30 m² trở lên</option>
                        </select>
                    </div>

                    <!-- Nút tìm kiếm -->
                    <div class="col-12 col-md-2 d-grid pt-md-3">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold py-2 shadow-sm rounded-pill">
                            <i class="bi bi-search me-1"></i> Tìm Kiếm
                        </button>
                    </div>
                </form>
            </div>

            <!-- QUICK FILTER PILLS (BẤM 1 CHẠM) -->
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                <a href="{{ route('home') }}" class="quick-filter-chip {{ !$propertyId && !$priceRange && !$minArea ? 'active' : '' }}">
                    Tất cả
                </a>
                @foreach($properties as $prop)
                    <a href="{{ route('home', ['property_id' => $prop->id]) }}#danh-sach-phong" 
                       class="quick-filter-chip {{ $propertyId == $prop->id ? 'active' : '' }}">
                        <i class="bi bi-building"></i> {{ $prop->name }}
                    </a>
                @endforeach
                <a href="{{ route('home', ['price_range' => 'under_3m']) }}#danh-sach-phong" 
                   class="quick-filter-chip {{ $priceRange === 'under_3m' ? 'active' : '' }}">
                    Dưới 3 triệu
                </a>
                <a href="{{ route('home', ['price_range' => '3m_5m']) }}#danh-sach-phong" 
                   class="quick-filter-chip {{ $priceRange === '3m_5m' ? 'active' : '' }}">
                    Từ 3 - 5 triệu
                </a>
                <a href="{{ route('home', ['price_range' => 'above_5m']) }}#danh-sach-phong" 
                   class="quick-filter-chip {{ $priceRange === 'above_5m' ? 'active' : '' }}">
                    Trên 5 triệu
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN LISTINGS -->
    <main class="py-4 flex-grow-1" id="danh-sach-phong">
        <div class="container">
            <!-- HEADING ROW -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fs-5 fw-bold text-dark mb-0">
                        Phòng Trống Cho Thuê
                    </h2>
                    <span class="text-muted small">Hiện có <b>{{ $vacantRooms->total() }}</b> phòng sẵn sàng dọn vào ở ngay</span>
                </div>

                @if($propertyId || $priceRange || $minPrice || $maxPrice || $minArea || $search)
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
                    </a>
                @endif
            </div>

            <!-- ROOM GRID (GỌN GÀNG, SÁCH SẼ, CHUẨN AIRBNB / PHONGTRO123) -->
            <div class="row g-3">
                @forelse($vacantRooms as $room)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="room-card">
                            <!-- THUMBNAIL PHÒNG -->
                            <div class="room-thumb">
                                <i class="bi bi-house-door room-thumb-icon"></i>
                                <span class="badge bg-success thumb-badge-left rounded-pill px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Trống
                                </span>
                                <span class="badge bg-dark bg-opacity-75 thumb-badge-right rounded-pill px-2 py-1">
                                    Tầng {{ $room->floor }}
                                </span>
                            </div>

                            <!-- CARD CONTENT -->
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <!-- TÊN PHÒNG & TÒA NHÀ -->
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <h3 class="fs-6 fw-bold text-dark mb-0">
                                        Phòng {{ $room->room_number }}
                                    </h3>
                                    <span class="badge bg-light text-primary border small">
                                        {{ $room->property->name }}
                                    </span>
                                </div>

                                <!-- ĐỊA CHỈ RÚT GỌN -->
                                <div class="text-muted small text-truncate mb-2" title="{{ $room->property->address }}">
                                    <i class="bi bi-geo-alt text-danger me-1"></i>{{ $room->property->address }}, {{ $room->property->city }}
                                </div>

                                <!-- GIÁ THUÊ -->
                                <div class="mb-2">
                                    <span class="price-text">{{ number_format($room->price, 0, ',', '.') }} đ</span>
                                    <span class="text-muted small">/tháng</span>
                                </div>

                                <!-- THÔNG SỐ CƠ BẢN (1 DÒNG GỌN) -->
                                <div class="small text-muted py-2 border-top border-bottom d-flex justify-content-between mb-2">
                                    <span><i class="bi bi-aspect-ratio text-primary me-1"></i><b>{{ $room->area }} m²</b></span>
                                    <span><i class="bi bi-people text-success me-1"></i>Tối đa <b>{{ $room->max_tenants }} ng</b></span>
                                    <span><i class="bi bi-lightning-charge text-warning me-1"></i><b>{{ number_format($room->electricity_rate) }}đ</b></span>
                                </div>

                                <!-- TIỆN ÍCH CHÍNH (3 CHIP GỌN) -->
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @php
                                        $displayAssets = $room->assets->take(4);
                                    @endphp
                                    @forelse($displayAssets as $asset)
                                        <span class="chip-amenity"><i class="bi bi-check text-success"></i>{{ $asset->name }}</span>
                                    @empty
                                        <span class="chip-amenity"><i class="bi bi-check text-success"></i>Điều hòa</span>
                                        <span class="chip-amenity"><i class="bi bi-check text-success"></i>Nóng lạnh</span>
                                        <span class="chip-amenity"><i class="bi bi-check text-success"></i>Wifi</span>
                                        <span class="chip-amenity"><i class="bi bi-check text-success"></i>Vân tay</span>
                                    @endforelse
                                </div>

                                <!-- NÚT LIÊN HỆ -->
                                <div class="mt-auto">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100 fw-bold rounded-pill"
                                            onclick="openContactModal('{{ $room->room_number }}', '{{ $room->property->name }}', '{{ $room->property->address }}', '{{ number_format($room->price, 0, ',', '.') }}đ')">
                                        <i class="bi bi-telephone-fill me-1"></i> Liên Hệ Xem Phòng
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center">
                        <div class="p-4 bg-white rounded-3 border border-secondary-subtle d-inline-block" style="max-width: 480px;">
                            <i class="bi bi-search text-muted fs-1 mb-2 d-block"></i>
                            <h3 class="fs-6 fw-bold text-dark">Không có phòng trống theo tiêu chí này</h3>
                            <p class="text-muted small mb-3">Vui lòng điều chỉnh lại bộ lọc mức giá hoặc chọn cơ sở khác.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                Xem tất cả phòng
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- PHÂN TRANG -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $vacantRooms->links() }}
            </div>
        </div>
    </main>

    <!-- TIÊU CHUẨN TIỆN ÍCH (GỌN GÀNG 4 CỘT) -->
    <section class="py-4 bg-white border-top" id="tieu-chuan">
        <div class="container">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="benefit-item">
                        <i class="bi bi-fingerprint text-primary fs-3 mb-2 d-block"></i>
                        <h4 class="fs-6 fw-bold mb-1">Khóa Vân Tay 24/7</h4>
                        <p class="text-muted small mb-0">Cửa chính tự động đóng, camera giám sát hành lang.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="benefit-item">
                        <i class="bi bi-clock-history text-success fs-3 mb-2 d-block"></i>
                        <h4 class="fs-6 fw-bold mb-1">Giờ Giấc Tự Do</h4>
                        <p class="text-muted small mb-0">Không chung chủ, thoải mái đi lại bất kỳ thời gian nào.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="benefit-item">
                        <i class="bi bi-qr-code-scan text-warning fs-3 mb-2 d-block"></i>
                        <h4 class="fs-6 fw-bold mb-1">Thanh Toán VietQR</h4>
                        <p class="text-muted small mb-0">Hóa đơn điện tử hàng tháng, quét mã QR tự động điền tiền.</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="benefit-item">
                        <i class="bi bi-tools text-info fs-3 mb-2 d-block"></i>
                        <h4 class="fs-6 fw-bold mb-1">Báo Hỏng Trực Tuyến</h4>
                        <p class="text-muted small mb-0">Gửi yêu cầu sửa chữa trên Cổng Khách Thuê dễ dàng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL LIÊN HỆ GỌN GÀNG -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header py-2 border-0">
                    <h5 class="modal-title fs-6 fw-bold text-dark">Liên Hệ Xem Phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0 text-center">
                    <div class="p-2 bg-light rounded-3 small mb-3 text-start">
                        <div><b>Phòng:</b> <span class="text-primary fw-bold" id="modalRoomNumber"></span></div>
                        <div><b>Tòa nhà:</b> <span id="modalPropertyName"></span></div>
                        <div><b>Giá:</b> <span class="text-success fw-bold" id="modalRoomPrice"></span></div>
                        <div class="text-muted" style="font-size: 0.75rem;" id="modalPropertyAddress"></div>
                    </div>

                    <a href="tel:0988888888" class="btn btn-primary w-100 fw-bold rounded-pill mb-2">
                        <i class="bi bi-telephone-fill me-1"></i> Gọi Hotline: 0988.888.888
                    </a>
                    <a href="https://zalo.me" target="_blank" class="btn btn-outline-success w-100 fw-bold rounded-pill">
                        <i class="bi bi-chat-dots me-1"></i> Nhắn Tin Zalo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER GỌN GÀNG -->
    <footer class="py-3">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                <div>
                    <b>NhaTroPro</b> — Bản quyền © 2026 <b>Lê Duy Khánh</b>. All Rights Reserved.
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('portal.index') }}" class="text-secondary text-decoration-none">Cổng Khách Thuê</a>
                    <a href="{{ route('login') }}" class="text-secondary text-decoration-none">Đăng Nhập</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openContactModal(roomNumber, propName, propAddress, roomPrice) {
            document.getElementById('modalRoomNumber').textContent = 'Phòng ' + roomNumber;
            document.getElementById('modalPropertyName').textContent = propName;
            document.getElementById('modalRoomPrice').textContent = roomPrice + '/tháng';
            document.getElementById('modalPropertyAddress').textContent = '📍 ' + propAddress;
            var modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        }
    </script>
</body>
</html>
