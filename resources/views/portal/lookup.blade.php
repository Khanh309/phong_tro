<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Tra Cứu Tiền Phòng Trọ Online - NhaTroPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; min-height: 100vh; }
        .lookup-box { max-width: 650px; margin: 40px auto; }
    </style>
</head>
<body>

<div class="container lookup-box">
    <!-- BANNER -->
    <div class="text-center mb-4">
        <div class="d-inline-flex p-3 bg-primary text-white rounded-circle mb-3 shadow">
            <i class="bi bi-buildings-fill fs-2"></i>
        </div>
        <h3 class="fw-bold text-dark">Tra Cứu Tiền Phòng Trọ Online</h3>
        <p class="text-muted">Nhập Số điện thoại của bạn hoặc Mã hóa đơn để xem chi tiết tiền điện, nước và thanh toán</p>
    </div>

    <!-- FORM TRA CỨU -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('portal.lookup') }}">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Số Điện Thoại Đăng Ký Thuê Phòng:</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white"><i class="bi bi-telephone-fill text-primary"></i></span>
                        <input type="tel" name="phone" class="form-control" placeholder="vd: 0912345678" value="{{ $phone }}">
                    </div>
                </div>

                <div class="text-center text-muted small my-2">--- HOẶC ---</div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Mã Hóa Đơn (nếu có trên tin nhắn):</label>
                    <input type="text" name="code" class="form-control" placeholder="vd: HD-202609-101" value="{{ $invoiceCode }}">
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                    <i class="bi bi-search me-1"></i> Tra Cứu Hóa Đơn Ngay
                </button>
            </form>
        </div>
    </div>

    <!-- KẾT QUẢ TRA CỨU -->
    @if($phone || $invoiceCode)
        @if($invoices->isEmpty())
            <div class="card p-4 text-center shadow-sm">
                <i class="bi bi-receipt-cutoff text-muted fs-1 mb-2"></i>
                <h5>Không tìm thấy hóa đơn nào</h5>
                <p class="text-muted mb-0">Vui lòng kiểm tra lại số điện thoại hoặc liên hệ với Chủ nhà / Quản lý trọ để được hỗ trợ.</p>
            </div>
        @else
            <h5 class="fw-bold mb-3 text-dark">
                @if($tenant)
                    Khách thuê: <span class="text-primary">{{ $tenant->name }}</span> ({{ $invoices->count() }} hóa đơn)
                @else
                    Kết quả tìm thấy {{ $invoices->count() }} hóa đơn:
                @endif
            </h5>

            <div class="row g-3">
                @foreach($invoices as $inv)
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">Hóa Đơn Tháng {{ $inv->month }}/{{ $inv->year }}</h5>
                                        <div class="text-muted small">Phòng {{ $inv->room->room_number }} - {{ $inv->room->property->name }}</div>
                                    </div>
                                    <span class="badge bg-{{ $inv->status_badge }}-subtle text-{{ $inv->status_badge }} fs-6">
                                        {{ $inv->status_label }}
                                    </span>
                                </div>

                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="d-flex justify-content-between small py-1 border-bottom">
                                        <span>Tiền phòng:</span>
                                        <span class="fw-bold">{{ number_format($inv->room_price) }}đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between small py-1 border-bottom">
                                        <span>Tiền điện ({{ $inv->electricity_usage }} kWh):</span>
                                        <span class="fw-bold text-warning-emphasis">{{ number_format($inv->electricity_total) }}đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between small py-1 border-bottom">
                                        <span>Tiền nước ({{ $inv->water_usage }} số):</span>
                                        <span class="fw-bold text-info-emphasis">{{ number_format($inv->water_total) }}đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between small py-1 border-bottom">
                                        <span>Phí dịch vụ:</span>
                                        <span class="fw-bold">{{ number_format($inv->other_fees) }}đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between fs-5 pt-2 text-dark">
                                        <span class="fw-bold">Tổng tiền:</span>
                                        <span class="fw-bold text-primary">{{ number_format($inv->total_amount) }}đ</span>
                                    </div>
                                    @if($inv->status !== 'paid')
                                        <div class="d-flex justify-content-between pt-1 text-danger fw-bold">
                                            <span>Còn phải đóng:</span>
                                            <span>{{ number_format($inv->remaining_amount) }}đ</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Hạn thanh toán: {{ $inv->due_date->format('d/m/Y') }}</span>
                                    <a href="{{ route('portal.show', $inv->invoice_code) }}" class="btn btn-primary btn-sm px-3 fw-bold">
                                        <i class="bi bi-qr-code-scan me-1"></i> Quét VietQR Thanh Toán
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>

</body>
</html>
