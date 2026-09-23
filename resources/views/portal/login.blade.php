<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Cổng Khách Thuê Trọ - NhaTroPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 580px; }
    </style>
</head>
<body class="p-3">

<div class="login-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex p-3 bg-primary text-white rounded-4 mb-3 shadow-lg">
            <i class="bi bi-person-badge-fill fs-1"></i>
        </div>
        <h3 class="fw-bold text-white mb-1">CỔNG DÀNH CHO NGƯỜI THUÊ TRỌ</h3>
        <p class="text-secondary">Tra Cứu Tiền Phòng Trọ Online, quét mã VietQR thanh toán, báo hỏng hóc và gửi khiếu nại</p>
    </div>

    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- 1. ĐĂNG NHẬP CHÍNH THỨC QUA TÀI KHOẢN (BẢO MẬT NHẤT) -->
    <div class="card border-0 shadow-lg text-white mb-4 rounded-4" style="background-color: #1e293b !important;">
        <div class="card-body p-4 text-center">
            <i class="bi bi-shield-lock-fill text-primary fs-2 mb-2 d-inline-block"></i>
            <h5 class="fw-bold text-white mb-2">Đăng Nhập Bằng Tài Khoản</h5>
            <p class="text-secondary small mb-3">Dành cho khách thuê đã được Chủ nhà/Admin cấp tài khoản (Email & Mật khẩu)</p>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 fw-bold shadow">
                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Với Email & Mật Khẩu
            </a>
        </div>
    </div>

    <!-- 2. HOẶC TRA CỨU BẰNG SỐ ĐIỆN THOẠI (DÀNH CHO KHÁCH CHƯA ĐẶT MẬT KHẨU) -->
    <div class="card border-0 shadow-lg text-white mb-4 rounded-4" style="background-color: #1e293b !important;">
        <div class="card-body p-4">
            <form action="{{ route('portal.index') }}" method="GET">
                <label class="form-label small fw-semibold text-secondary mb-2">
                    <i class="bi bi-telephone-fill me-1 text-info"></i> Hoặc tra cứu nhanh bằng số điện thoại:
                </label>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-secondary border-0 text-white"><i class="bi bi-telephone-fill"></i></span>
                    <input type="tel" name="phone" class="form-control bg-dark border-secondary text-white" placeholder="vd: 0912345678" required>
                    <button class="btn btn-outline-info" type="submit">Tra Cứu</button>
                </div>
                <div class="form-text text-secondary" style="font-size: 0.75rem;">
                    * Lưu ý: Khách thuê đã được cấp tài khoản cần đăng nhập bằng mật khẩu để bảo vệ thông tin cá nhân.
                </div>
            </form>
        </div>
    </div>

    <!-- 3. KHÁCH THUÊ CHỌN NHANH (CHỈ HIỂN THỊ TRONG MÔI TRƯỜNG PHÁT TRIỂN / DEMO) -->
    @if((config('app.env') !== 'production' || config('app.debug')) && $allTenants->isNotEmpty())
        <div class="card border-0 shadow-lg bg-dark text-white mb-4 rounded-4" style="background-color: #1e293b !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 text-white">Chế Độ Thử Nghiệm: Chọn Nhanh</h6>
                    </div>
                    <span class="badge bg-warning text-dark small">Demo Only</span>
                </div>

                <form action="{{ route('portal.select') }}" method="POST">
                    @csrf
                    <div class="list-group list-group-flush rounded-3 mb-3">
                        @foreach($allTenants as $t)
                            <label class="list-group-item list-group-item-action bg-slate border-secondary text-white d-flex justify-content-between align-items-center py-2 px-3" style="background-color: #334155; cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="tenant_id" value="{{ $t->id }}" {{ $loop->first ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold small">{{ $t->name }}</div>
                                        <div class="text-secondary" style="font-size: 0.75rem;">
                                            Phòng <b class="text-warning">{{ $t->currentContract?->room?->room_number }}</b> - {{ $t->currentContract?->room?->property?->name }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary small">{{ $t->phone }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-outline-warning btn-sm w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Chọn Nhanh Vào Cổng (Demo)
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="text-center mt-3">
        <a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Trở về Trang Quản Trị Chủ Nhà
        </a>
    </div>
</div>

</body>
</html>
