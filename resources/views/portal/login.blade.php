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

    <!-- KHÁCH THUÊ CHỌN NHANH (1-CLICK TRẢI NGHIỆM NGAY) -->
    <div class="card border-0 shadow-lg bg-dark text-white mb-4 rounded-4" style="background-color: #1e293b !important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-lightning-charge-fill text-warning fs-5"></i>
                <h6 class="fw-bold mb-0 text-white">Bấm Chọn Nhanh Tài Khoản Khách Thuê:</h6>
            </div>

            <form action="{{ route('portal.select') }}" method="POST">
                @csrf
                <div class="list-group list-group-flush rounded-3 mb-3">
                    @foreach($allTenants as $t)
                        <label class="list-group-item list-group-item-action bg-slate border-secondary text-white d-flex justify-content-between align-items-center py-3" style="background-color: #334155; cursor: pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <input class="form-check-input flex-shrink-0" type="radio" name="tenant_id" value="{{ $t->id }}" {{ $loop->first ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-bold">{{ $t->name }}</div>
                                    <div class="text-secondary small">
                                        Phòng <b class="text-warning">{{ $t->currentContract->room->room_number }}</b> - {{ $t->currentContract->room->property->name }}
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">{{ $t->phone }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Vào Cổng Khách Thuê Ngay
                </button>
            </form>
        </div>
    </div>

    <!-- HOẶC NHẬP SỐ ĐIỆN THOẠI -->
    <div class="card border-0 shadow-lg text-white rounded-4" style="background-color: #1e293b !important;">
        <div class="card-body p-4">
            <form action="{{ route('portal.index') }}" method="GET">
                <label class="form-label small fw-semibold text-secondary mb-2">Hoặc đăng nhập bằng số điện thoại:</label>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-secondary border-0 text-white"><i class="bi bi-telephone-fill"></i></span>
                    <input type="tel" name="phone" class="form-control bg-dark border-secondary text-white" placeholder="vd: 0912345678" required>
                    <button class="btn btn-outline-primary" type="submit">Đăng nhập</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('dashboard') }}" class="text-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Trở về Trang Quản Trị Chủ Nhà
        </a>
    </div>
</div>

</body>
</html>
