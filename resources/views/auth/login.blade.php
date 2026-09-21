<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống Quản Lý Nhà Trọ - NhaTroPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .login-wrapper {
            width: 100%;
            max-width: 520px;
        }
        .card-custom {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: none;
        }
        .demo-btn {
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #f8fafc;
            text-align: left;
            width: 100%;
        }
        .demo-btn:hover {
            border-color: #2563eb;
            background-color: #eff6ff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- LOGO & TIÊU ĐỀ -->
    <div class="text-center mb-4">
        <div class="d-inline-flex p-3 bg-primary text-white rounded-4 shadow-lg mb-3">
            <i class="bi bi-buildings-fill fs-1"></i>
        </div>
        <h3 class="fw-bold text-white mb-1">HỆ THỐNG QUẢN LÝ NHÀ TRỌ</h3>
        <p class="text-secondary small mb-0">Vui lòng đăng nhập để phân quyền quản trị theo vai trò</p>
    </div>

    <!-- KHUNG ĐĂNG NHẬP CHÍNH -->
    <div class="card card-custom p-4 p-sm-5 mb-4">
        <h5 class="fw-bold text-dark mb-3">Đăng Nhập Tài Khoản</h5>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small py-2" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="loginForm" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Email đăng nhập <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" id="emailInput" class="form-control border-start-0" placeholder="admin@nhatro.vn" value="{{ old('email', 'admin@nhatro.vn') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Mật khẩu <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control border-start-0" placeholder="••••••" value="123456" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                    <label class="form-check-label small text-muted" for="rememberMe">Ghi nhớ đăng nhập</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow">
                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Hệ Thống
            </button>
        </form>

        <hr class="my-4">

        <!-- CHỌN NHANH ĐỂ TEST PHÂN QUYỀN (1-CLICK DEMO) -->
        <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold small text-dark"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Bấm 1-Click Để Thử Phân Quyền:</span>
                <span class="badge bg-light text-muted small">Tự động điền & Đăng nhập</span>
            </div>

            <div class="d-grid gap-2">
                <!-- NÚT 1: ADMIN -->
                <button type="button" class="demo-btn" onclick="quickLogin('admin@nhatro.vn', '123456')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-primary small">👑 Chủ Nhà Trọ (Admin Tổng Thể)</div>
                            <div class="text-muted" style="font-size: 0.76rem;">Duy nhất 1 Admin quản lý toàn bộ chuỗi nhà trọ, lãi ròng & tài chính</div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">Toàn quyền</span>
                    </div>
                </button>

                <!-- NÚT 2: MANAGER CẦU GIẤY -->
                <button type="button" class="demo-btn" onclick="quickLogin('quanly.caugiay@nhatro.vn', '123456')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-success small">👔 Quản Lý Nhà Cầu Giấy (Manager)</div>
                            <div class="text-muted" style="font-size: 0.76rem;">Chỉ xem phòng, hóa đơn, khách thuê Tòa Cầu Giấy</div>
                        </div>
                        <span class="badge bg-success-subtle text-success">Cầu Giấy</span>
                    </div>
                </button>

                <!-- NÚT 3: MANAGER THANH XUÂN -->
                <button type="button" class="demo-btn" onclick="quickLogin('quanly.thanhxuan@nhatro.vn', '123456')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-info small">👔 Quản Lý Nhà Thanh Xuân (Manager)</div>
                            <div class="text-muted" style="font-size: 0.76rem;">Chỉ xem phòng, hóa đơn, khách thuê Căn hộ Thanh Xuân</div>
                        </div>
                        <span class="badge bg-info-subtle text-info">Thanh Xuân</span>
                    </div>
                </button>

                <!-- NÚT 4: KHÁCH THUÊ PHÒNG (TENANT) -->
                <button type="button" class="demo-btn" onclick="quickLogin('hoang.tran@gmail.com', '123456')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-warning small">🏠 Khách Thuê Phòng (Tenant)</div>
                            <div class="text-muted" style="font-size: 0.76rem;">Trần Văn Hoàng (P.101 Cầu Giấy) - Vào Cổng Khách Thuê tra cứu & quét mã</div>
                        </div>
                        <span class="badge bg-warning-subtle text-warning">Khách Thuê</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- DÀNH CHO KHÁCH THUÊ TRỌ -->
    <div class="card border-0 rounded-4 p-3 text-center" style="background-color: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white small px-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill text-info fs-5"></i>
                <span class="text-start">Bạn là <b>Người thuê trọ</b> muốn xem hóa đơn & quét mã VietQR?</span>
            </div>
            <a href="{{ route('portal.index') }}" class="btn btn-sm btn-info text-dark fw-bold">
                Vào Cổng Khách Thuê <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
    function quickLogin(email, pass) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = pass;
        document.getElementById('loginForm').submit();
    }
</script>

</body>
</html>
