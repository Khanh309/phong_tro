<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hợp Đồng Thuê Phòng Trọ - P.{{ $contract->room->room_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 14pt; line-height: 1.5; color: #000; background: #fff; padding: 20px; }
        .contract-box { max-width: 800px; margin: 0 auto; }
        h4, h5, h6 { font-family: "Times New Roman", Times, serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="contract-box">
    <div class="no-print mb-4 d-flex justify-content-between align-items-center p-3 bg-light border rounded">
        <div><b>HỢP ĐỒNG THUÊ PHÒNG TRỌ</b> - Mã: {{ $contract->contract_code }}</div>
        <button onclick="window.print()" class="btn btn-primary fw-bold">In Hợp Đồng (Ctrl + P)</button>
    </div>

    <!-- TIÊU NGỮ -->
    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h5>
        <div class="fw-bold">Độc lập - Tự do - Hạnh phúc</div>
        <div class="my-1">---------------o0o---------------</div>
        <h3 class="fw-bold mt-3 mb-1">HỢP ĐỒNG THUÊ PHÒNG TRỌ</h3>
        <div class="fst-italic small">Số: {{ $contract->contract_code }}</div>
    </div>

    <p>Hôm nay, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}, tại địa chỉ: {{ $contract->room->property->address }}, {{ $contract->room->property->district }}, {{ $contract->room->property->city }}. Chúng tôi gồm có:</p>

    <!-- BÊN CHO THUÊ -->
    <div class="mb-3">
        <h6 class="fw-bold text-uppercase">BÊN CHO THUÊ (BÊN A):</h6>
        <div>- Đại diện cơ sở: <b>{{ $contract->room->property->bank_account_holder ?: 'Chủ Nhà Trọ' }}</b></div>
        <div>- Địa chỉ cơ sở: {{ $contract->room->property->address }}, {{ $contract->room->property->district }}, {{ $contract->room->property->city }}</div>
        <div>- Tên nhà trọ: <b>{{ $contract->room->property->name }}</b></div>
        <div>- Số tài khoản thanh toán: {{ $contract->room->property->bank_account_number }} tại Ngân hàng {{ $contract->room->property->bank_name }}</div>
    </div>

    <!-- BÊN THUÊ -->
    <div class="mb-3">
        <h6 class="fw-bold text-uppercase">BÊN THUÊ (BÊN B):</h6>
        <div>- Họ và tên: <b>{{ $contract->tenant->name }}</b> (Giới tính: {{ $contract->tenant->gender }})</div>
        <div>- Số CCCD/CMND: <b>{{ $contract->tenant->id_card_number }}</b> (Ngày cấp: {{ $contract->tenant->id_card_date ? $contract->tenant->id_card_date->format('d/m/Y') : '---' }} tại {{ $contract->tenant->id_card_place }})</div>
        <div>- Ngày sinh: {{ $contract->tenant->dob ? $contract->tenant->dob->format('d/m/Y') : '---' }} | Quê quán: {{ $contract->tenant->hometown }}</div>
        <div>- Số điện thoại liên hệ: <b>{{ $contract->tenant->phone }}</b> | Biển số xe: {{ $contract->tenant->vehicle_plate ?: 'Không' }}</div>
    </div>

    <p>Hai bên cùng thống nhất ký kết hợp đồng thuê phòng trọ với các điều khoản sau:</p>

    <div class="mb-3">
        <div class="fw-bold">ĐIỀU 1: PHÒNG THUÊ VÀ GIÁ CẢ</div>
        <div>1.1. Bên A đồng ý cho Bên B thuê <b>Phòng số {{ $contract->room->room_number }}</b> (Tầng {{ $contract->room->floor }}), diện tích <b>{{ $contract->room->area }} m²</b>.</div>
        <div>1.2. Giá tiền thuê phòng: <b>{{ number_format($contract->rental_price, 0, ',', '.') }} VNĐ / tháng</b> (Bằng chữ: ........................................................................................................).</div>
        <div>1.3. Tiền đặt cọc: <b>{{ number_format($contract->deposit_amount, 0, ',', '.') }} VNĐ</b>. Tiền cọc này sẽ được hoàn trả cho Bên B sau khi kết thúc hợp đồng và đã thanh toán đủ các khoản tiền điện, nước, dịch vụ và không làm hư hỏng tài sản.</div>
        <div>1.4. Thời hạn thuê: Từ ngày <b>{{ $contract->start_date->format('d/m/Y') }}</b> đến ngày <b>{{ $contract->end_date->format('d/m/Y') }}</b>.</div>
    </div>

    <div class="mb-3">
        <div class="fw-bold">ĐIỀU 2: TIỀN ĐIỆN, NƯỚC VÀ DỊCH VỤ</div>
        <div>- Tiền điện: Tính theo công tơ riêng của phòng với đơn giá: <b>{{ number_format($contract->room->electricity_rate) }} VNĐ / kWh</b>. Chỉ số điện ban đầu lúc bàn giao: <b>{{ $contract->room->initial_electricity }} kWh</b> (Công tơ số: {{ $contract->room->electricity_meter_number }}).</div>
        <div>- Tiền nước: Đơn giá <b>{{ number_format($contract->room->water_rate) }} VNĐ</b> ({{ $contract->room->water_type_name }}).</div>
        <div>- Tiền mạng Internet, rác thải và các dịch vụ khác thanh toán theo định mức hàng tháng của cơ sở.</div>
    </div>

    <!-- BIÊN BẢN BÀN GIAO TÀI SẢN KÈM THEO -->
    @if($contract->room->assets->isNotEmpty())
        <div class="mb-3">
            <div class="fw-bold">ĐIỀU 3: BIÊN BẢN BÀN GIAO TÀI SẢN & THIẾT BỊ TRONG PHÒNG</div>
            <table class="table table-bordered table-sm mt-2">
                <thead>
                    <tr class="text-center">
                        <th>STT</th>
                        <th>Tên Thiết Bị / Tài Sản</th>
                        <th>Số Lượng</th>
                        <th>Tình Trạng Lúc Bàn Giao</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->room->assets as $idx => $asset)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ $asset->name }}</td>
                            <td class="text-center">{{ $asset->quantity }}</td>
                            <td>{{ $asset->condition }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mb-4">
        <div class="fw-bold">ĐIỀU 4: NỘI QUY & TRÁCH NHIỆM HAI BÊN</div>
        <div style="white-space: pre-line;">{{ $contract->terms }}</div>
    </div>

    <!-- KÝ TÊN -->
    <div class="row mt-5 pt-3 text-center">
        <div class="col-6">
            <div class="fw-bold text-uppercase mb-5">ĐẠI DIỆN BÊN B (BÊN THUÊ)</div>
            <div class="fst-italic text-muted small">(Ký và ghi rõ họ tên)</div>
            <div class="mt-4 fw-bold">{{ $contract->tenant->name }}</div>
        </div>
        <div class="col-6">
            <div class="fw-bold text-uppercase mb-5">ĐẠI DIỆN BÊN A (BÊN CHO THUÊ)</div>
            <div class="fst-italic text-muted small">(Ký và ghi rõ họ tên)</div>
            <div class="mt-4 fw-bold">{{ $contract->room->property->bank_account_holder ?: 'Chủ Nhà Trọ' }}</div>
        </div>
    </div>
</div>

</body>
</html>
