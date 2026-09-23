<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NhaTroPro.vn - Kênh Cho Thuê Phòng Trọ, Căn Hộ Mini Chính Chủ Giá Tốt</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Inter & Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-red: #e03c31;
            --primary-red-hover: #c52d23;
            --primary-blue: #0056b3;
            --dark-header: #121b22;
            --bg-main: #f5f5f5;
            --price-color: #e03c31;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Roboto', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-main);
            color: #333333;
            margin: 0;
            padding: 0;
        }

        /* Top Announcement Bar */
        .topbar-announcement {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.82rem;
            color: #475569;
            padding: 6px 0;
        }

        /* Main Header */
        .main-header {
            background-color: #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .site-logo {
            font-size: 1.55rem;
            font-weight: 900;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .site-logo span {
            color: var(--primary-red);
        }

        .site-tagline {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 500;
            display: block;
            margin-top: -3px;
        }

        /* Search Box Real-Estate Style (Phongtro123 Style) */
        .search-container {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 24px 0 20px;
        }

        .search-card-pro {
            background-color: #febb02;
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(254, 187, 2, 0.25);
        }

        .search-inner {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .btn-search-pro {
            background-color: var(--primary-red);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            transition: background-color 0.15s ease;
        }

        .btn-search-pro:hover {
            background-color: var(--primary-red-hover);
            color: #ffffff;
        }

        /* Breadcrumb Bar */
        .breadcrumb-section {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 0;
            font-size: 0.85rem;
        }

        /* Listing Cards (Horizontal / Real-Estate Style) */
        .listing-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .listing-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .listing-thumb-wrap {
            position: relative;
            overflow: hidden;
            height: 100%;
            min-height: 200px;
            background-color: #e2e8f0;
        }

        .listing-thumb-img {
            width: 100%;
            height: 100%;
            min-height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .listing-card:hover .listing-thumb-img {
            transform: scale(1.04);
        }

        .badge-verified {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #16a34a;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .badge-photo-count {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.65);
            color: #ffffff;
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .listing-title {
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.4;
            color: var(--primary-red);
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .listing-title:hover {
            color: #b91c1c;
            text-decoration: underline;
        }

        .listing-price {
            font-size: 1.35rem;
            font-weight: 800;
            color: #16a34a;
        }

        .listing-area {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
        }

        .listing-location {
            font-size: 0.85rem;
            color: #64748b;
        }

        .listing-desc {
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .amenity-tag {
            font-size: 0.75rem;
            background-color: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .btn-call-landlord {
            background-color: #16a34a;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            border-radius: 6px;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-call-landlord:hover {
            background-color: #15803d;
            color: #ffffff;
        }

        .btn-zalo-chat {
            background-color: #0068ff;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            border-radius: 6px;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-zalo-chat:hover {
            background-color: #0056d6;
            color: #ffffff;
        }

        /* Sidebar Widgets */
        .sidebar-widget {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 2px solid var(--primary-red);
            padding-bottom: 8px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .sidebar-link-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #f1f5f9;
            color: #334155;
            text-decoration: none;
            font-size: 0.88rem;
        }

        .sidebar-link-item:hover {
            color: var(--primary-red);
            font-weight: 600;
            padding-left: 4px;
            transition: all 0.15s ease;
        }

        /* Sort Bar */
        .sort-bar {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .sort-btn-group a {
            font-size: 0.82rem;
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: #475569;
            border: 1px solid #cbd5e1;
            margin-left: 4px;
            background-color: #ffffff;
        }

        .sort-btn-group a.active, .sort-btn-group a:hover {
            background-color: var(--primary-red);
            color: #ffffff;
            border-color: var(--primary-red);
        }

        /* Footer */
        .site-footer {
            background-color: #1e293b;
            color: #94a3b8;
            font-size: 0.85rem;
            border-top: 3px solid var(--primary-red);
        }

        .site-footer h6 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 14px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .site-footer a {
            color: #cbd5e1;
            text-decoration: none;
        }

        .site-footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- TOPBAR ANNOUNCEMENT -->
    <div class="topbar-announcement">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-none d-md-block">
                <i class="bi bi-shield-check text-success me-1"></i>
                Hệ thống Quản lý & Cho thuê Nhà trọ <b>NhaTroPro</b> — Phòng đẹp, chính chủ 100%, không phí môi giới
            </div>
            <div class="d-flex align-items-center gap-3 ms-auto">
                <span class="text-danger fw-bold"><i class="bi bi-telephone-fill"></i> Hotline: 0988.888.888</span>
                <span class="text-muted">|</span>
                <a href="{{ route('portal.index') }}" class="text-secondary text-decoration-none">
                    <i class="bi bi-receipt me-1"></i> Tra Cứu Hóa Đơn Khách Thuê
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <header class="main-header py-2">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- LOGO & BRAND -->
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger text-white rounded p-1 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="bi bi-buildings-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="site-logo">NhaTro<span>Pro</span>.vn</span>
                            <span class="site-tagline">KÊNH TÌM PHÒNG TRỌ & QUẢN LÝ CHO THUÊ</span>
                        </div>
                    </div>
                </a>

                <!-- NAV LINKS -->
                <nav class="d-none d-lg-flex align-items-center gap-4 fw-medium text-dark">
                    <a href="{{ route('home') }}" class="text-danger text-decoration-none fw-bold">
                        <i class="bi bi-house-door-fill me-1"></i> Trang Chủ
                    </a>
                    <a href="#danh-sach-phong" class="text-dark text-decoration-none">
                        Cho Thuê Phòng Trọ (<b>{{ $totalVacantCount }}</b>)
                    </a>
                    <a href="#co-so-chuoi" class="text-dark text-decoration-none">
                        Chuỗi Tòa Nhà (<b>{{ $properties->count() }}</b> cơ sở)
                    </a>
                    <a href="{{ route('portal.index') }}" class="text-dark text-decoration-none">
                        Cổng Khách Thuê
                    </a>
                </nav>

                <!-- AUTH / DASHBOARD BUTTON -->
                <div class="d-flex align-items-center gap-2">
                    @auth
                        @if(auth()->user()->isTenant())
                            <a href="{{ route('portal.index') }}" class="btn btn-outline-primary btn-sm fw-bold">
                                <i class="bi bi-person-circle me-1"></i> Khách thuê: {{ auth()->user()->name }}
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                                <i class="bi bi-speedometer2 me-1"></i> Bảng Quản Trị
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm fw-bold px-3 py-1">
                            <i class="bi bi-person-fill me-1"></i> Đăng Nhập
                        </a>
                        <a href="tel:0988888888" class="btn btn-danger btn-sm fw-bold px-3 py-1 d-none d-sm-inline-flex align-items-center gap-1">
                            <i class="bi bi-telephone-fill"></i> Xem Phòng Ngay
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- REAL ESTATE SEARCH SECTION (PHONGTRO123 STYLE) -->
    <section class="search-container">
        <div class="container">
            <div class="text-center mb-3">
                <h1 class="fw-bold text-dark fs-3 mb-1">
                    Kênh Tìm Thuê Phòng Trọ, Căn Hộ Mini & Nhà Trọ Chính Chủ
                </h1>
                <p class="text-muted small mb-0">
                    Phòng sạch đẹp, khép kín, đầy đủ điều hòa, nóng lạnh, khóa vân tay, giờ giấc 100% tự do tại Hà Nội
                </p>
            </div>

            <!-- BỘ LỌC TÌM KIẾM BẤT ĐỘNG SẢN VÀNG NỔI BẬT -->
            <div class="search-card-pro mx-auto" style="max-width: 1040px;">
                <div class="search-inner">
                    <form method="GET" action="{{ route('home') }}#danh-sach-phong" class="row g-2 align-items-center">
                        <!-- Tỉnh / Thành phố cố định -->
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Tỉnh / Thành Phố:
                            </label>
                            <select class="form-select form-select-sm bg-light" disabled>
                                <option>Hà Nội (Toàn thành phố)</option>
                            </select>
                        </div>

                        <!-- Cơ sở / Tòa nhà / Quận Huyện -->
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-building text-primary me-1"></i> Khu vực / Tòa nhà:
                            </label>
                            <select name="property_id" class="form-select form-select-sm">
                                <option value="">Tất cả khu vực ({{ $properties->count() }} cơ sở)</option>
                                @foreach($properties as $prop)
                                    <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>
                                        📍 {{ $prop->name }} ({{ $prop->available_rooms_count }} phòng)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Khoảng giá -->
                        <div class="col-6 col-md-2">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-cash-stack text-success me-1"></i> Mức giá thuê:
                            </label>
                            <select name="price_range" class="form-select form-select-sm">
                                <option value="">Tất cả mức giá</option>
                                <option value="under_3m" {{ $priceRange === 'under_3m' ? 'selected' : '' }}>Dưới 3 triệu</option>
                                <option value="3m_5m" {{ $priceRange === '3m_5m' ? 'selected' : '' }}>Từ 3 - 5 triệu</option>
                                <option value="above_5m" {{ $priceRange === 'above_5m' ? 'selected' : '' }}>Trên 5 triệu</option>
                            </select>
                        </div>

                        <!-- Diện tích -->
                        <div class="col-6 col-md-2">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-arrows-angle-expand text-info me-1"></i> Diện tích:
                            </label>
                            <select name="min_area" class="form-select form-select-sm">
                                <option value="">Tất cả diện tích</option>
                                <option value="20" {{ $minArea == '20' ? 'selected' : '' }}>Từ 20 m² trở lên</option>
                                <option value="25" {{ $minArea == '25' ? 'selected' : '' }}>Từ 25 m² trở lên</option>
                                <option value="30" {{ $minArea == '30' ? 'selected' : '' }}>Từ 30 m² trở lên</option>
                            </select>
                        </div>

                        <!-- Nút tìm kiếm -->
                        <div class="col-12 col-md-2 d-grid pt-md-3">
                            <button type="submit" class="btn btn-search-pro btn-sm">
                                <i class="bi bi-search me-1"></i> Tìm Kiếm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- BREADCRUMB -->
    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Cho thuê phòng trọ Hà Nội</a></li>
                    @if($propertyId)
                        @php
                            $selectedProperty = $properties->firstWhere('id', $propertyId);
                        @endphp
                        <li class="breadcrumb-item active text-danger fw-bold">{{ $selectedProperty ? $selectedProperty->name : 'Khu vực lọc' }}</li>
                    @else
                        <li class="breadcrumb-item active text-danger fw-bold">Tất cả phòng trống</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>

    <!-- MAIN BODY: 2 CỘT CHUẨN THỰC TẾ (CỘT TRÁI 8 CỘT, CỘT PHẢI 4 CỘT) -->
    <div class="container py-4" id="danh-sach-phong">
        <div class="row g-4">
            
            <!-- CỘT CHÍNH (8 CỘT): DANH SÁCH PHÒNG TRỌ CHO THUÊ -->
            <div class="col-12 col-lg-8">
                
                <!-- THANH TIÊU ĐỀ & SẮP XẾP (SORT BAR) -->
                <div class="sort-bar">
                    <div>
                        <h2 class="fs-6 fw-bold text-dark mb-0">
                            Danh Sách Phòng Trọ Đang Cho Thuê
                        </h2>
                        <small class="text-muted">Hiện có <b>{{ $vacantRooms->total() }}</b> tin đăng phòng trống thực tế</small>
                    </div>

                    <!-- NHÓM NÚT SẮP XẾP -->
                    <div class="sort-btn-group d-flex align-items-center">
                        <span class="small text-muted me-1">Sắp xếp:</span>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" class="{{ $sort === 'newest' ? 'active' : '' }}">Mới nhất</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="{{ $sort === 'price_asc' ? 'active' : '' }}">Giá thấp</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="{{ $sort === 'price_desc' ? 'active' : '' }}">Giá cao</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'area_desc']) }}" class="{{ $sort === 'area_desc' ? 'active' : '' }}">Diện tích</a>
                    </div>
                </div>

                @if($propertyId || $priceRange || $minPrice || $maxPrice || $minArea || $search)
                    <div class="alert alert-info py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-funnel-fill me-1"></i> Đang lọc kết quả tìm kiếm phòng trọ
                        </div>
                        <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm py-0 px-2 text-decoration-none">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    </div>
                @endif

                <!-- DANH SÁCH THẺ PHÒNG TRỌ (ẢNH THỰC TẾ & BỐ CỤC CHUYÊN NGHIỆP) -->
                @php
                    $realRoomImages = [
                        'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=700&q=80',
                        'https://images.unsplash.com/photo-1540518614846-7ede433c4550?auto=format&fit=crop&w=700&q=80',
                    ];
                @endphp

                @forelse($vacantRooms as $index => $room)
                    @php
                        $roomPhoto = $room->primary_image_url;
                        $displayPrice = number_format($room->price, 0, ',', '.') . ' đ/tháng';
                        $roomImagesJson = json_encode($room->all_image_urls);
                    @endphp
                    <div class="listing-card">
                        <div class="row g-0">
                            <!-- CỘT ẢNH (4 CỘT) -->
                            <div class="col-12 col-md-4">
                                <div class="listing-thumb-wrap">
                                    <img src="{{ $roomPhoto }}" alt="Phòng {{ $room->room_number }} - {{ $room->property->name }}" class="listing-thumb-img" loading="lazy">
                                    <span class="badge-verified">
                                        <i class="bi bi-patch-check-fill me-1"></i>Chính Chủ
                                    </span>
                                    <span class="badge-photo-count">
                                        <i class="bi bi-camera-fill me-1"></i>{{ $room->images_count }} ảnh
                                    </span>
                                </div>
                            </div>

                            <!-- CỘT NỘI DUNG (8 CỘT) -->
                            <div class="col-12 col-md-8 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <!-- TIÊU ĐỀ BÀI ĐĂNG THỰC TẾ -->
                                    <h3 class="mb-1">
                                        <a href="javascript:void(0)" class="listing-title" onclick='openContactModal("{{ $room->room_number }}", "{{ $room->property->name }}", "{{ $room->property->address }}", "{{ $displayPrice }}", {{ $roomImagesJson }})'>
                                            CHO THUÊ PHÒNG P{{ $room->room_number }} KHÉP KÍN FULL ĐỒ — {{ mb_strtoupper($room->property->name) }}
                                        </a>
                                    </h3>

                                    <!-- DÒNG GIÁ & DIỆN TÍCH BẮT MẮT -->
                                    <div class="d-flex align-items-baseline gap-3 mb-2">
                                        <span class="listing-price">{{ $displayPrice }}</span>
                                        <span class="listing-area"><i class="bi bi-aspect-ratio text-primary me-1"></i>{{ $room->area }} m²</span>
                                        <span class="badge bg-light text-secondary border small">Tầng {{ $room->floor }}</span>
                                    </div>

                                    <!-- VỊ TRÍ ĐỊA CHỈ -->
                                    <div class="listing-location mb-2">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        <b>{{ $room->property->name }}:</b> {{ $room->property->address }}, {{ $room->property->district ? $room->property->district . ', ' : '' }}{{ $room->property->city }}
                                    </div>

                                    <!-- MÔ TẢ NGẮN CHÂN THỰC -->
                                    <p class="listing-desc mb-2">
                                        {{ $room->description ?: "Phòng trọ khép kín mới đẹp, thoáng mát, giờ giấc tự do không chung chủ. Trang bị sẵn điều hòa, bình nóng lạnh, cửa khóa vân tay an toàn, camera 24/7, bãi đỗ xe rộng rãi." }}
                                    </p>

                                    <!-- DÒNG ĐƠN GIÁ ĐIỆN NƯỚC MINH BẠCH -->
                                    <div class="small text-muted mb-2 d-flex flex-wrap gap-3 bg-light p-2 rounded">
                                        <span>⚡ Điện: <b>{{ number_format($room->electricity_rate) }}đ/kWh</b></span>
                                        <span>💧 Nước: <b>{{ $room->water_calculation_type === 'per_person' ? number_format($room->water_rate) . 'đ/người' : ($room->water_calculation_type === 'fixed_room' ? number_format($room->water_rate) . 'đ/phòng' : number_format($room->water_rate) . 'đ/m³') }}</b></span>
                                        <span>👥 Tối đa: <b>{{ $room->max_tenants }} người</b></span>
                                    </div>

                                    <!-- TIỆN ÍCH CHÍNH -->
                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        @forelse($room->assets->take(4) as $asset)
                                            <span class="amenity-tag"><i class="bi bi-check2 text-success"></i>{{ $asset->name }}</span>
                                        @empty
                                            <span class="amenity-tag"><i class="bi bi-check2 text-success"></i>Điều hòa</span>
                                            <span class="amenity-tag"><i class="bi bi-check2 text-success"></i>Nóng lạnh</span>
                                            <span class="amenity-tag"><i class="bi bi-check2 text-success"></i>Wifi</span>
                                            <span class="amenity-tag"><i class="bi bi-check2 text-success"></i>Khóa vân tay</span>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- THÔNG TIN LIÊN HỆ & NÚT GỌI / ZALO -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 border-top gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Quản Lý Tòa Nhà <i class="bi bi-patch-check-fill text-primary" title="Đã xác minh chính chủ"></i></div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Cập nhật hôm nay</div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="tel:0988888888" class="btn-call-landlord">
                                            <i class="bi bi-telephone-fill"></i> 0988.888.888
                                        </a>
                                        <a href="https://zalo.me" target="_blank" class="btn-zalo-chat">
                                            <i class="bi bi-chat-dots-fill"></i> Nhắn Zalo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="listing-card p-5 text-center">
                        <i class="bi bi-search text-muted fs-1 mb-2 d-block"></i>
                        <h4 class="fs-6 fw-bold text-dark">Không tìm thấy phòng trọ nào phù hợp với yêu cầu này!</h4>
                        <p class="text-muted small mb-3">Bạn thử nới rộng khoảng giá hoặc chọn xem tất cả các khu vực cơ sở khác nhé.</p>
                        <a href="{{ route('home') }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i> Xem Tất Cả Các Phòng
                        </a>
                    </div>
                @endforelse

                <!-- PHÂN TRANG -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $vacantRooms->links() }}
                </div>
            </div>

            <!-- CỘT PHẢI (4 CỘT): SIDEBAR TIỆN ÍCH & DANH MỤC THỰC TẾ -->
            <div class="col-12 col-lg-4">
                
                <!-- WIDGET 1: XEM THEO KHOẢNG GIÁ -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-title">Xem Theo Khoảng Giá</h3>
                    <div>
                        <a href="{{ route('home', ['price_range' => 'under_3m']) }}#danh-sach-phong" class="sidebar-link-item">
                            <span><i class="bi bi-chevron-right text-danger me-1"></i> Dưới 3 triệu</span>
                            <span class="badge bg-light text-secondary border">Phòng giá rẻ</span>
                        </a>
                        <a href="{{ route('home', ['price_range' => '3m_5m']) }}#danh-sach-phong" class="sidebar-link-item">
                            <span><i class="bi bi-chevron-right text-danger me-1"></i> Từ 3 - 5 triệu</span>
                            <span class="badge bg-light text-secondary border">Phổ biến nhất</span>
                        </a>
                        <a href="{{ route('home', ['price_range' => 'above_5m']) }}#danh-sach-phong" class="sidebar-link-item">
                            <span><i class="bi bi-chevron-right text-danger me-1"></i> Trên 5 triệu</span>
                            <span class="badge bg-light text-secondary border">Studio / Cao cấp</span>
                        </a>
                        <a href="{{ route('home') }}#danh-sach-phong" class="sidebar-link-item">
                            <span><i class="bi bi-chevron-right text-danger me-1"></i> Tất cả mức giá</span>
                            <span class="badge bg-danger text-white">{{ $totalVacantCount }}</span>
                        </a>
                    </div>
                </div>

                <!-- WIDGET 2: CHUỖI CÁC CƠ SỞ ĐANG VẬN HÀNH -->
                <div class="sidebar-widget" id="co-so-chuoi">
                    <h3 class="sidebar-title">Chuỗi Tòa Nhà Cho Thuê</h3>
                    <div>
                        @foreach($properties as $prop)
                            <a href="{{ route('home', ['property_id' => $prop->id]) }}#danh-sach-phong" class="sidebar-link-item">
                                <div>
                                    <div class="fw-bold">{{ $prop->name }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $prop->district ? $prop->district . ', ' : '' }}{{ $prop->city }}</small>
                                </div>
                                <span class="badge bg-success text-white">{{ $prop->available_rooms_count }} phòng trống</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- WIDGET 3: CAM KẾT CHẤT LƯỢNG CHÍNH CHỦ -->
                <div class="sidebar-widget bg-light border-0 shadow-sm">
                    <h3 class="sidebar-title text-success" style="border-bottom-color: #16a34a;">Cam Kết Của NhaTroPro</h3>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span><b>100% Tin thật, phòng thật:</b> Đúng hình ảnh, đúng giá niêm yết, không phát sinh chi phí ẩn.</span>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span><b>Không thu phí môi giới:</b> Khách hàng xem phòng và ký hợp đồng trực tiếp với Ban Quản Lý.</span>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span><b>An ninh tuyệt đối:</b> Cửa khóa vân tay độc lập, hệ thống camera 24/24, giờ giấc tự do không chung chủ.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span><b>Thanh toán minh bạch:</b> Hóa đơn điện tử gửi thẳng qua ứng dụng, quét mã VietQR tự động.</span>
                        </li>
                    </ul>
                </div>

                <!-- WIDGET 4: HỖ TRỢ DẪN XEM PHÒNG TRỰC TIẾP -->
                <div class="sidebar-widget text-center border-danger-subtle bg-danger-subtle bg-opacity-25">
                    <div class="p-2">
                        <i class="bi bi-headset text-danger fs-1 mb-2 d-block"></i>
                        <h4 class="fs-6 fw-bold text-dark mb-1">Cần Tìm Phòng Gấp?</h4>
                        <p class="text-muted small mb-3">Gọi ngay Hotline Ban Quản Lý để được hỗ trợ dẫn xem phòng miễn phí trong 15 phút!</p>
                        <a href="tel:0988888888" class="btn btn-danger btn-sm fw-bold w-100 py-2 shadow-sm mb-2">
                            <i class="bi bi-telephone-fill me-1"></i> GỌI 0988.888.888
                        </a>
                        <a href="https://zalo.me" target="_blank" class="btn btn-outline-primary btn-sm fw-bold w-100 py-2">
                            <i class="bi bi-chat-dots-fill me-1"></i> Chat Zalo Với Quản Lý
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL LIÊN HỆ XEM PHÒNG CHUYÊN NGHIỆP -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title fs-6 fw-bold mb-0">
                        <i class="bi bi-house-check-fill me-2"></i>Liên Hệ Xem & Thuê Phòng Trọ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="alert alert-light border small text-start mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Phòng bạn quan tâm:</span>
                            <b class="text-danger fs-6" id="modalRoomNumber"></b>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Tòa nhà / Cơ sở:</span>
                            <b class="text-dark" id="modalPropertyName"></b>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Giá thuê niêm yết:</span>
                            <b class="text-success fs-6" id="modalRoomPrice"></b>
                        </div>
                        <div class="text-muted mt-2 pt-2 border-top" style="font-size: 0.8rem;" id="modalPropertyAddress"></div>
                    </div>

                    <!-- THUMBNAIL GALLERY TRONG MODAL -->
                    <div id="modalGalleryContainer" class="mb-3 text-start">
                        <div class="small fw-semibold text-muted mb-1"><i class="bi bi-images text-primary me-1"></i>Hình ảnh thực tế của phòng (Bấm để phóng to):</div>
                        <div id="modalGallery" class="d-flex gap-2 overflow-x-auto pb-2"></div>
                    </div>

                    <div class="my-3">
                        <p class="small text-muted mb-3">Bạn vui lòng gọi trực tiếp hoặc nhắn tin Zalo để hẹn lịch xem phòng và nhận tư vấn chi tiết:</p>
                        <a href="tel:0988888888" class="btn btn-danger btn-lg w-100 fw-bold mb-2 shadow-sm">
                            <i class="bi bi-telephone-fill me-1"></i> Gọi Hotline: 0988.888.888
                        </a>
                        <a href="https://zalo.me" target="_blank" class="btn btn-outline-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-chat-dots-fill me-1"></i> Nhắn Tin Trực Tiếp Qua Zalo
                        </a>
                    </div>
                    <div class="text-muted" style="font-size: 0.78rem;">
                        <i class="bi bi-clock me-1"></i> Thời gian hỗ trợ dẫn xem phòng: <b>8:00 - 21:30</b> tất cả các ngày trong tuần.
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm w-100" data-bs-dismiss="modal">Đóng cửa sổ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTION FOOTER (CHUẨN BẤT ĐỘNG SẢN VIỆT NAM) -->
    <footer class="site-footer py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <h5 class="text-white fw-bold mb-2">NhaTro<span class="text-danger">Pro</span>.vn</h5>
                    <p class="small text-muted mb-3">
                        Hệ thống thông tin và quản lý chuỗi phòng trọ, căn hộ dịch vụ và chung cư mini cao cấp tại Hà Nội. Cam kết 100% phòng thật, giá thật, chính chủ và không qua trung gian.
                    </p>
                    <div class="small text-light mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Văn phòng điều hành: Tòa nhà NhaTroPro, Cầu Giấy, Hà Nội
                    </div>
                    <div class="small text-light mb-1">
                        <i class="bi bi-telephone-fill text-success me-1"></i> Hotline: 0988.888.888 (24/7)
                    </div>
                    <div class="small text-light">
                        <i class="bi bi-envelope-fill text-warning me-1"></i> Email: lienhe@nhatro.vn
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <h6>Về Chúng Tôi</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a href="{{ route('home') }}">Giới thiệu chuỗi nhà trọ</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}#co-so-chuoi">Danh sách cơ sở tòa nhà</a></li>
                        <li class="mb-2"><a href="{{ route('portal.index') }}">Cổng tra cứu khách thuê</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Đăng nhập quản trị viên</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-2">
                    <h6>Chính Sách & Quy Định</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a href="javascript:void(0)">Nội quy phòng trọ</a></li>
                        <li class="mb-2"><a href="javascript:void(0)">Quy trình check-in & nhận phòng</a></li>
                        <li class="mb-2"><a href="javascript:void(0)">Hướng dẫn đăng ký tạm trú</a></li>
                        <li class="mb-2"><a href="javascript:void(0)">Chính sách bảo mật thông tin</a></li>
                    </ul>
                </div>

                <div class="col-12 col-md-3">
                    <h6>Bản Quyền & Phát Triển</h6>
                    <p class="small text-muted mb-2">
                        Bản quyền © 2026 <b>Lê Duy Khánh</b>. Toàn quyền bảo lưu (All Rights Reserved).
                    </p>
                    <div class="p-2 bg-dark rounded border border-secondary small text-muted">
                        Mã nguồn và kiến trúc hệ thống là tài sản trí tuệ độc quyền của <b>Lê Duy Khánh</b>. Nghiêm cấm sao chép dưới mọi hình thức.
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 mt-4 text-center small text-muted">
                Copyright © 2026 <b>Lê Duy Khánh</b> — Hệ Thống Quản Lý Chuỗi Nhà Trọ NhaTroPro.vn.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openContactModal(roomNumber, propName, propAddress, roomPrice, images) {
            document.getElementById('modalRoomNumber').textContent = 'Phòng ' + roomNumber;
            document.getElementById('modalPropertyName').textContent = propName;
            document.getElementById('modalRoomPrice').textContent = roomPrice;
            document.getElementById('modalPropertyAddress').innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> <b>Địa chỉ:</b> ' + propAddress;

            var gallery = document.getElementById('modalGallery');
            var container = document.getElementById('modalGalleryContainer');
            gallery.innerHTML = '';

            if (images && images.length > 0) {
                images.forEach(function(imgUrl) {
                    var a = document.createElement('a');
                    a.href = imgUrl;
                    a.target = '_blank';
                    a.className = 'flex-shrink-0';
                    a.innerHTML = '<img src="' + imgUrl + '" class="rounded border shadow-sm" style="width: 100px; height: 75px; object-fit: cover;" alt="Ảnh phòng">';
                    gallery.appendChild(a);
                });
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }

            var modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        }
    </script>
</body>
</html>
