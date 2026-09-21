<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Tiền Nhà - P.{{ $invoice->room->room_number }} - T{{ $invoice->month }}/{{ $invoice->year }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 13pt; line-height: 1.4; color: #000; background: #fff; padding: 20px; }
        .invoice-box { max-width: 800px; margin: 0 auto; border: 1px dashed #999; padding: 30px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .invoice-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="no-print mb-4 d-flex justify-content-between align-items-center p-3 bg-light border rounded">
        <div><b>PHIẾU THU TIỀN NHÀ & DỊCH VỤ</b> - Mã HĐ: {{ $invoice->invoice_code }}</div>
        <button onclick="window.print()" class="btn btn-primary fw-bold">In Hóa Đơn (Ctrl + P)</button>
    </div>

    <!-- TIÊU ĐỀ -->
    <div class="row align-items-center mb-4">
        <div class="col-8">
            <h5 class="fw-bold text-uppercase mb-1">{{ $invoice->room->property->name }}</h5>
            <div class="small">Địa chỉ: {{ $invoice->room->property->address }}, {{ $invoice->room->property->district }}</div>
            <div class="small">Hotline / Zalo: <b>{{ $invoice->room->property->bank_account_holder }}</b></div>
        </div>
        <div class="col-4 text-end">
            <h4 class="fw-bold text-uppercase text-primary mb-1">PHIẾU THU TIỀN NHÀ</h4>
            <div class="small">Kỳ: <b>Tháng {{ $invoice->month }}/{{ $invoice->year }}</b></div>
            <div class="small text-muted">Số: {{ $invoice->invoice_code }}</div>
        </div>
    </div>

    <hr>

    <div class="row mb-3 small">
        <div class="col-6">
            <div>Phòng: <b class="fs-6">Phòng {{ $invoice->room->room_number }}</b> (Tầng {{ $invoice->room->floor }})</div>
            <div>Khách thuê: <b>{{ $invoice->contract?->tenant?->name ?? '---' }}</b></div>
            <div>Điện thoại: {{ $invoice->contract?->tenant?->phone ?? '---' }}</div>
        </div>
        <div class="col-6 text-end">
            <div>Ngày lập: {{ $invoice->created_at->format('d/m/Y') }}</div>
            <div>Hạn nộp: <b class="text-danger">{{ $invoice->due_date->format('d/m/Y') }}</b></div>
            <div>Trạng thái: <b>{{ $invoice->status_label }}</b></div>
        </div>
    </div>

    <!-- BẢNG CHI TIẾT -->
    <table class="table table-bordered table-sm small mb-3">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 40px;">STT</th>
                <th>Khoản Mục Thanh Toán</th>
                <th style="width: 140px;">Chỉ Số Cũ</th>
                <th style="width: 140px;">Chỉ Số Mới</th>
                <th style="width: 100px;">Số Lượng</th>
                <th style="width: 120px;">Đơn Giá</th>
                <th style="width: 150px;">Thành Tiền (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="fw-bold">Tiền thuê phòng</td>
                <td class="text-center">---</td>
                <td class="text-center">---</td>
                <td class="text-center">1 tháng</td>
                <td class="text-end">{{ number_format($invoice->room_price) }}đ</td>
                <td class="text-end fw-bold">{{ number_format($invoice->room_price) }}đ</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td class="fw-bold">Tiền điện sinh hoạt</td>
                <td class="text-center">{{ $invoice->electricity_old }}</td>
                <td class="text-center">{{ $invoice->electricity_new }}</td>
                <td class="text-center"><b>{{ $invoice->electricity_usage }} kWh</b></td>
                <td class="text-end">{{ number_format($invoice->electricity_rate) }}đ</td>
                <td class="text-end fw-bold">{{ number_format($invoice->electricity_total) }}đ</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td class="fw-bold">Tiền nước sinh hoạt</td>
                <td class="text-center">{{ $invoice->water_old }}</td>
                <td class="text-center">{{ $invoice->water_new }}</td>
                <td class="text-center"><b>{{ $invoice->water_usage }} số</b></td>
                <td class="text-end">{{ number_format($invoice->water_rate) }}đ</td>
                <td class="text-end fw-bold">{{ number_format($invoice->water_total) }}đ</td>
            </tr>

            @if(!empty($invoice->fees_detail) && is_array($invoice->fees_detail))
                @php $stt = 4; @endphp
                @foreach($invoice->fees_detail as $fee)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $fee['name'] }}</td>
                        <td class="text-center">---</td>
                        <td class="text-center">---</td>
                        <td class="text-center">1 gói</td>
                        <td class="text-end">{{ number_format($fee['amount']) }}đ</td>
                        <td class="text-end fw-bold">{{ number_format($fee['amount']) }}đ</td>
                    </tr>
                @endforeach
            @endif

            @if($invoice->discount > 0)
                <tr>
                    <td colspan="6" class="text-end fw-bold">Giảm trừ:</td>
                    <td class="text-end fw-bold text-danger">-{{ number_format($invoice->discount) }}đ</td>
                </tr>
            @endif

            <tr class="fs-6">
                <td colspan="6" class="text-end fw-bold text-uppercase">TỔNG CỘNG TIỀN PHẢI NỘP:</td>
                <td class="text-end fw-bold text-primary">{{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</td>
            </tr>
            <tr>
                <td colspan="6" class="text-end text-muted">Đã thanh toán:</td>
                <td class="text-end fw-semibold text-success">{{ number_format($invoice->paid_amount, 0, ',', '.') }} VNĐ</td>
            </tr>
            <tr class="fs-6 table-warning">
                <td colspan="6" class="text-end fw-bold text-danger text-uppercase">SỐ TIỀN CÒN PHẢI THU:</td>
                <td class="text-end fw-bold text-danger">{{ number_format($invoice->remaining_amount, 0, ',', '.') }} VNĐ</td>
            </tr>
        </tbody>
    </table>

    <!-- VIETQR VÀ THÔNG TIN CHUYỂN KHOẢN -->
    <div class="row align-items-center mb-4">
        <div class="col-8 small">
            <div class="fw-bold mb-1">THÔNG TIN CHUYỂN KHOẢN NGÂN HÀNG:</div>
            <div>- Ngân hàng: <b>{{ $invoice->room->property->bank_name }}</b></div>
            <div>- Số tài khoản: <b>{{ $invoice->room->property->bank_account_number }}</b></div>
            <div>- Chủ tài khoản: <b>{{ $invoice->room->property->bank_account_holder }}</b></div>
            <div>- Nội dung chuyển khoản: <code>P{{ $invoice->room->room_number }} T{{ $invoice->month }} {{ $invoice->year }}</code></div>
            <div class="text-muted mt-1 fst-italic">Vui lòng nộp đúng số tiền trước ngày {{ $invoice->due_date->format('d/m/Y') }}. Trân trọng cảm ơn!</div>
        </div>
        <div class="col-4 text-center">
            @if($invoice->viet_qr_url && $invoice->remaining_amount > 0)
                <img src="{{ $invoice->viet_qr_url }}" alt="VietQR" style="max-height: 140px;" class="border p-1">
                <div style="font-size: 0.75rem;" class="text-muted mt-1">Quét mã để thanh toán</div>
            @endif
        </div>
    </div>

    <!-- CHỮ KÝ -->
    <div class="row text-center mt-4">
        <div class="col-6">
            <div class="fw-bold text-uppercase">NGƯỜI NỘP TIỀN</div>
            <div class="fst-italic small text-muted">(Ký và ghi rõ họ tên)</div>
        </div>
        <div class="col-6">
            <div class="fw-bold text-uppercase">ĐẠI DIỆN THU TIỀN</div>
            <div class="fst-italic small text-muted">(Ký và ghi rõ họ tên)</div>
        </div>
    </div>
</div>

</body>
</html>
