<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Phòng Trọ Tiện Nghi, Giá Tốt & An Ninh - NhaTroPro</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary: #0f172a;
            --accent: #10b981;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-light);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .public-navbar {
            background: #ffffff;
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.06);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--secondary) !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
            color: #ffffff;
            padding: 70px 0 90px;
            position: relative;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.75rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #cbd5e1;
            max-width: 680px;
        }

        /* Search Box */
        .search-box {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.18);
            padding: 24px;
            margin-top: -45px;
            position: relative;
            z-index: 20;
        }

        /* Room Cards */
        .room-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .room-badge {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 9999px;
        }

        .price-tag {
            font-size: 1.55rem;
            font-weight: 800;
            color: #16a34a;
        }

        .feature-chip {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Advantage cards */
        .advantage-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            height: 100%;
        }

        .advantage-card:hover {
            border-color: var(--primary);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.08);
        }

        /* Footer */
        footer {
            background-color: #0f172a;
            color: #94a3b8;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="public-navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- LOGO & BRAND -->
                <a href="{{ route('home') }}" class="navbar-brand text-decoration-none">
                    <div class="bg-primary text-white rounded-3 p-2 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                        <i class="bi bi-buildings-fill fs-5"></i>
                    </div>
                    <span>NhaTro<span class="text-primary">Pro</span></span>
                </a>

                <!-- NAV LINKS -->
                <div class="d-none d-md-flex align-items-center gap-4">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none fw-semibold">
                        <i class="bi bi-house-door-fill text-primary me-1"></i> Trang Chủ
                    </a>
                    <a href="#vacant-rooms" class="text-secondary text-decoration-none fw-medium">
                        <i class="bi bi-door-open me-1"></i> Phòng Trống ({{ $totalVacantCount }})
                    </a>
                    <a href="#features" class="text-secondary text-decoration-none fw-medium">
                        <i class="bi bi-stars text-warning me-1"></i> Tiện Ích & An Ninh
                    </a>
                    <a href="{{ route('portal.index') }}" class="text-secondary text-decoration-none fw-medium">
                        <i class="bi bi-receipt-cutoff text-info me-1"></i> Cổng Khách Thuê
                    </a>
                </div>

                <!-- CTA BUTTONS (ĐĂNG NHẬP / BẢNG QUẢN TRỊ) -->
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('portal.index') }}" class="btn btn-outline-secondary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-receipt"></i> Tra Cứu Hóa Đơn
                    </a>

                    @auth
                        @if(auth()->user()->isTenant())
                            <a href="{{ route('portal.index') }}" class="btn btn-info btn-sm fw-bold shadow-sm d-flex align-items-center gap-1">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm fw-bold shadow-sm d-flex align-items-center gap-1">
                                <i class="bi bi-speedometer2"></i> Vào Bảng Quản Trị
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Đăng Nhập</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO BANNER -->
    <header class="hero-section">
        <div class="container text-center text-md-start">
            <div class="row align-items-center gy-4">
                <div class="col-12 col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 mb-3 small border border-white border-opacity-10">
                        <span class="badge bg-success rounded-pill">Đang có sẵn</span>
                        <span><b>{{ $totalVacantCount }}</b> phòng trống dọn vào ở ngay hôm nay</span>
                    </div>
                    <h1 class="hero-title text-white mb-3">
                        Tìm Thuê Phòng Trọ Tiện Nghi, Giá Tốt & An Ninh 24/7
                    </h1>
                    <p class="hero-subtitle mb-4">
                        Chuỗi phòng trọ & căn hộ mini hiện đại: Khóa cửa vân tay, camera giám sát 24/7, giờ giấc hoàn toàn tự do, thanh toán tiền phòng quét mã VietQR tự động.
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                        <a href="#vacant-rooms" class="btn btn-success btn-lg fw-bold px-4 shadow">
                            <i class="bi bi-search me-1"></i> Xem Danh Sách Phòng Trống
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-info-circle me-1"></i> Tiện Ích Phòng Trọ
                        </a>
                    </div>
                </div>

                <div class="col-12 col-lg-4 text-center d-none d-lg-block">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-10 backdrop-blur">
                        <div class="display-4 fw-bold text-warning mb-1">{{ $properties->count() }}</div>
                        <div class="text-white-50 mb-4">Cơ sở / Tòa nhà đang vận hành</div>
                        <div class="d-flex justify-content-around text-white border-top border-white border-opacity-10 pt-3">
                            <div>
                                <h4 class="fw-bold mb-0 text-success">{{ $totalVacantCount }}</h4>
                                <small class="text-white-50">Phòng trống</small>
                            </div>
                            <div class="border-start border-white border-opacity-10"></div>
                            <div>
                                <h4 class="fw-bold mb-0 text-info">100%</h4>
                                <small class="text-white-50">Giờ giấc tự do</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- BỘ LỌC TÌM KIẾM PHÒNG NHANH -->
    <div class="container">
        <div class="search-box">
            <form method="GET" action="{{ route('home') }}#vacant-rooms" class="row g-3 align-items-end">
                <!-- Cơ sở / Khu vực -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="bi bi-building text-primary me-1"></i> Khu vực / Tòa nhà:
                    </label>
                    <select name="property_id" class="form-select border-secondary-subtle">
                        <option value="">🏢 Tất cả các cơ sở ({{ $properties->count() }} tòa nhà)</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>
                                📍 {{ $prop->name }} ({{ $prop->available_rooms_count }} phòng trống)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Khoảng giá -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="bi bi-cash text-success me-1"></i> Giá từ (VNĐ):
                    </label>
                    <input type="number" name="min_price" class="form-control border-secondary-subtle" placeholder="vd: 2000000" step="100000" value="{{ $minPrice }}">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="bi bi-cash-stack text-success me-1"></i> Đến (VNĐ):
                    </label>
                    <input type="number" name="max_price" class="form-control border-secondary-subtle" placeholder="vd: 4500000" step="100000" value="{{ $maxPrice }}">
                </div>

                <!-- Diện tích -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="bi bi-arrows-angle-expand text-info me-1"></i> Diện tích từ:
                    </label>
                    <div class="input-group">
                        <input type="number" name="min_area" class="form-control border-secondary-subtle" placeholder="vd: 20" value="{{ $minArea }}">
                        <span class="input-group-text bg-light border-secondary-subtle small">m²</span>
                    </div>
                </div>

                <!-- Nút lọc -->
                <div class="col-6 col-md-8 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold py-2 shadow-sm">
                        <i class="bi bi-funnel-fill me-1"></i> Tìm Phòng
                    </button>
                    @if($propertyId || $minPrice || $maxPrice || $minArea || $search)
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary py-2" title="Xóa bộ lọc">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- DANH SÁCH PHÒNG TRỐNG -->
    <section class="py-5" id="vacant-rooms">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-door-open-fill text-success me-2"></i>Danh Sách Phòng Trọ Còn Trống
                    </h3>
                    <p class="text-muted mb-0">Các phòng đã được dọn vệ sinh sạch sẽ, đầy đủ thiết bị, sẵn sàng đón khách vào ở</p>
                </div>
                <div class="badge bg-success-subtle text-success fs-6 px-3 py-2 border border-success-subtle rounded-pill">
                    🟢 Có {{ $vacantRooms->total() }} phòng phù hợp
                </div>
            </div>

            <!-- GRID PHÒNG TRỐNG -->
            <div class="row g-4">
                @forelse($vacantRooms as $room)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="room-card shadow-sm">
                            <div class="p-4 flex-grow-1">
                                <!-- HEADER CARD -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge bg-success room-badge mb-2">🟢 Trống - Ở ngay</span>
                                        <h4 class="fw-bold text-dark mb-0">Phòng {{ $room->room_number }}</h4>
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-building text-primary me-1"></i> <b>{{ $room->property->name }}</b>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border px-2 py-1">Tầng {{ $room->floor }}</span>
                                </div>

                                <!-- GIÁ VÀ THÔNG SỐ -->
                                <div class="p-3 bg-light rounded-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                                        <span class="text-muted small">Giá thuê niêm yết:</span>
                                        <div>
                                            <span class="price-tag">{{ number_format($room->price, 0, ',', '.') }}đ</span>
                                            <small class="text-muted">/tháng</small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted pt-2 border-top">
                                        <span><i class="bi bi-aspect-ratio me-1 text-primary"></i> <b>{{ $room->area }} m²</b></span>
                                        <span><i class="bi bi-people me-1 text-success"></i> Tối đa <b>{{ $room->max_tenants }} người</b></span>
                                    </div>
                                </div>

                                <!-- BIỂU PHÍ ĐIỆN NƯỚC MINH BẠCH -->
                                <div class="small text-muted mb-3 d-flex justify-content-between px-1">
                                    <span>⚡ Điện: <b>{{ number_format($room->electricity_rate) }}đ/kWh</b></span>
                                    <span>💧 Nước: <b>{{ $room->water_calculation_type === 'per_person' ? number_format($room->water_rate) . 'đ/người' : ($room->water_calculation_type === 'fixed_room' ? number_format($room->water_rate) . 'đ/phòng' : number_format($room->water_rate) . 'đ/m³') }}</b></span>
                                </div>

                                <!-- ĐỊA CHỈ -->
                                <div class="small text-muted mb-3">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ $room->property->address }}, {{ $room->property->district ? $room->property->district . ', ' : '' }}{{ $room->property->city }}
                                </div>

                                <!-- TIỆN ÍCH / NỘI THẤT CÓ SẴN TRONG PHÒNG -->
                                <div class="mb-3">
                                    <div class="small fw-semibold text-dark mb-2">Tiện ích & Nội thất có sẵn:</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($room->assets as $asset)
                                            <span class="feature-chip"><i class="bi bi-check-circle-fill text-success"></i> {{ $asset->name }}</span>
                                        @empty
                                            <span class="feature-chip"><i class="bi bi-check-circle-fill text-success"></i> Điều hòa</span>
                                            <span class="feature-chip"><i class="bi bi-check-circle-fill text-success"></i> Nóng lạnh</span>
                                            <span class="feature-chip"><i class="bi bi-check-circle-fill text-success"></i> Wifi tốc độ cao</span>
                                            <span class="feature-chip"><i class="bi bi-check-circle-fill text-success"></i> Khóa vân tay</span>
                                        @endforelse
                                    </div>
                                </div>

                                @if($room->description)
                                    <div class="small text-secondary fst-italic mb-3 bg-light p-2 rounded">
                                        "{{ Str::limit($room->description, 100) }}"
                                    </div>
                                @endif
                            </div>

                            <!-- FOOTER CARD: NÚT LIÊN HỆ -->
                            <div class="p-3 bg-light border-top">
                                <button type="button" class="btn btn-success w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                                        onclick="openContactModal('{{ $room->room_number }}', '{{ $room->property->name }}', '{{ $room->property->address }}', '{{ number_format($room->price, 0, ',', '.') }}đ')">
                                    <i class="bi bi-telephone-fill"></i> Liên Hệ Xem Phòng Ngay
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="p-5 bg-white rounded-4 shadow-sm border border-secondary-subtle">
                            <i class="bi bi-emoji-smile text-primary fs-1 mb-3 d-inline-block"></i>
                            <h4 class="fw-bold text-dark">Hiện tại chưa có phòng trống theo tiêu chí lọc này!</h4>
                            <p class="text-muted mb-4">Toàn bộ phòng trọ trong mức giá hoặc cơ sở này đang được thuê kín. Bạn có thể chọn cơ sở khác hoặc bấm đặt lại bộ lọc.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-arrow-clockwise me-1"></i> Xem Tất Cả Các Phòng
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
    </section>

    <!-- TẠI SAO NÊN CHỌN NHÀ TRỌ CHÚNG TÔI -->
    <section class="py-5 bg-white border-top border-bottom" id="features">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold mb-2">TIỆN ÍCH NỔI BẬT</span>
                <h3 class="fw-bold text-dark">Tiêu Chuẩn Chuỗi Nhà Trọ Thông Minh</h3>
                <p class="text-muted">Chúng tôi cam kết mang đến không gian sống an toàn, sạch đẹp, văn minh và tiện lợi nhất cho mọi khách thuê</p>
            </div>

            <div class="row g-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="p-3 bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-fingerprint fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Khóa Vân Tay & An Ninh 24/7</h5>
                        <p class="text-muted small mb-0">Cửa ra vào tích hợp máy quét vân tay bảo mật, hệ thống camera hồng ngoại bao quát toàn bộ hành lang và bãi đỗ xe.</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="p-3 bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-clock-history fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Giờ Giấc 100% Tự Do</h5>
                        <p class="text-muted small mb-0">Không chung chủ, bạn tự do đi về bất cứ lúc nào, phù hợp cho người đi làm ca, sinh viên năng động.</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="p-3 bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-qr-code-scan fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Quét Mã VietQR Tiện Lợi</h5>
                        <p class="text-muted small mb-0">Hóa đơn tiền điện nước hàng tháng gửi thẳng lên Cổng Khách Thuê kèm mã QR tự động điền đúng số tiền và nội dung.</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="advantage-card text-center">
                        <div class="p-3 bg-info-subtle text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-tools fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Báo Hỏng Hóc Trực Tuyến</h5>
                        <p class="text-muted small mb-0">Khi bóng đèn, vòi nước hoặc điều hòa gặp sự cố, chỉ cần gửi báo hỏng trên web, thợ kỹ thuật sẽ tới hỗ trợ nhanh chóng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL LIÊN HỆ XEM PHÒNG -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-telephone-inbound-fill me-2"></i>Liên Hệ Xem & Thuê Phòng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border small mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Phòng bạn chọn:</span>
                            <b class="text-primary fs-6" id="modalRoomNumber"></b>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Tòa nhà:</span>
                            <b class="text-dark" id="modalPropertyName"></b>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Giá thuê:</span>
                            <b class="text-success fs-6" id="modalRoomPrice"></b>
                        </div>
                        <div class="mt-2 text-muted" id="modalPropertyAddress" style="font-size: 0.8rem;"></div>
                    </div>

                    <div class="text-center my-3">
                        <div class="small text-muted mb-2">Gọi điện trực tiếp hoặc nhắn tin Zalo để hẹn lịch xem phòng:</div>
                        <a href="tel:0988888888" class="btn btn-primary btn-lg w-100 fw-bold mb-2 shadow-sm">
                            <i class="bi bi-telephone-fill me-1"></i> Gọi Hotline: 0988.888.888
                        </a>
                        <a href="https://zalo.me" target="_blank" class="btn btn-outline-success w-100 fw-bold">
                            <i class="bi bi-chat-dots-fill me-1"></i> Nhắn Tin Trực Tiếp Qua Zalo
                        </a>
                    </div>
                    <div class="text-muted small text-center mt-3">
                        <i class="bi bi-clock me-1"></i> Giờ hỗ trợ dẫn khách xem phòng: <b>8:00 - 21:30</b> hàng ngày (kể cả Thứ 7 & CN).
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="py-4 mt-auto">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded p-1 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-buildings-fill small"></i>
                    </div>
                    <span class="fw-bold text-white">NhaTroPro</span>
                    <span class="text-muted small">| Hệ thống Chuỗi Phòng Trọ & Quản Lý Cho Thuê</span>
                </div>

                <div class="text-muted small">
                    Bản quyền © 2026 <b>Lê Duy Khánh</b>. All Rights Reserved.
                </div>

                <div class="d-flex gap-3 small">
                    <a href="{{ route('portal.index') }}" class="text-secondary text-decoration-none">Cổng Khách Thuê</a>
                    <a href="{{ route('login') }}" class="text-secondary text-decoration-none">Đăng Nhập Quản Trị</a>
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
            document.getElementById('modalPropertyAddress').innerHTML = '<i class="bi bi-geo-alt me-1"></i> Địa chỉ: ' + propAddress;
            var modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        }
    </script>
</body>
</html>
