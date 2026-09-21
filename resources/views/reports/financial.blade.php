@extends('layouts.app')

@section('title', 'Báo Cáo Tài Chính Thu - Chi & Thất Thoát')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-graph-up-arrow text-success me-2"></i>Báo Cáo Tài Chính Thu - Chi & Lợi Nhuận Ròng</h3>
        <p class="text-muted mb-0">Tách bạch Dòng Thu (Khách nộp) với Dòng Chi (Điện EVN, Nước tổng, Thuế nộp Nhà nước, Vận hành)</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-printer me-1"></i> In Báo Cáo
        </button>
    </div>
</div>

<!-- BỘ LỌC CƠ SỞ & THỜI GIAN -->
<div class="card p-3 mb-4 shadow-sm d-print-none">
    <form method="GET" action="{{ route('reports.financial') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <select name="property_id" class="form-select" onchange="this.form.submit()">
                <option value="">🏢 Tất cả các Nhà trọ</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ $propertyId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="month" class="form-select" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Xem báo cáo</button>
        </div>
    </form>
</div>

<!-- 3 THẺ TỔNG KẾT TÀI CHÍNH LỚN -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card p-3 border-success border-2 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted fw-bold small">DÒNG THU (TỪ KHÁCH THUÊ)</span>
                <i class="bi bi-wallet2 text-success fs-4"></i>
            </div>
            <h3 class="fw-bold text-success mb-1">{{ number_format($totalCollected, 0, ',', '.') }}đ</h3>
            <div class="text-muted small">Thực thu về tài khoản / Tiền mặt</div>
            <div class="mt-2 pt-2 border-top small d-flex justify-content-between">
                <span>Tổng hóa đơn phát hành:</span>
                <b>{{ number_format($totalInvoiced, 0, ',', '.') }}đ</b>
            </div>
            <div class="small d-flex justify-content-between text-danger">
                <span>Còn nợ chưa thu (chậm đóng):</span>
                <b>{{ number_format($totalDebt, 0, ',', '.') }}đ</b>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card p-3 border-danger border-2 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted fw-bold small">DÒNG CHI (TRẢ NHÀ NƯỚC & ĐỐI TÁC)</span>
                <i class="bi bi-cash-stack text-danger fs-4"></i>
            </div>
            <h3 class="fw-bold text-danger mb-1">{{ number_format($totalExpenses, 0, ',', '.') }}đ</h3>
            <div class="text-muted small">Tổng chi phí thực tế đã thanh toán</div>
            <div class="mt-2 pt-2 border-top small d-flex justify-content-between">
                <span>Điện tổng EVN:</span>
                <b>{{ number_format($expenseElecEvn, 0, ',', '.') }}đ</b>
            </div>
            <div class="small d-flex justify-content-between">
                <span>Thuế nộp Nhà nước:</span>
                <b>{{ number_format($expenseTax, 0, ',', '.') }}đ</b>
            </div>
            <div class="small d-flex justify-content-between">
                <span>Nước sạch + Rác + Cáp quang + Bảo trì:</span>
                <b>{{ number_format($expenseWaterSupply + $expenseWaste + $expenseInternet + $expenseMaintenance + $expenseOther, 0, ',', '.') }}đ</b>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card p-3 {{ $netProfitCash >= 0 ? 'border-primary' : 'border-danger' }} border-2 h-100 shadow-sm bg-light">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted fw-bold small">LỢI NHUẬN RÒNG (THU - CHI)</span>
                <i class="bi bi-trophy-fill {{ $netProfitCash >= 0 ? 'text-primary' : 'text-danger' }} fs-4"></i>
            </div>
            <h3 class="fw-bold {{ $netProfitCash >= 0 ? 'text-primary' : 'text-danger' }} mb-1">
                {{ ($netProfitCash >= 0 ? '+' : '') . number_format($netProfitCash, 0, ',', '.') }}đ
            </h3>
            <div class="text-muted small">Lợi nhuận ròng thực tế thu vào túi</div>
            <div class="mt-2 pt-2 border-top small d-flex justify-content-between">
                <span>Lợi nhuận dự kiến (nếu thu đủ 100% nợ):</span>
                <b class="text-dark">{{ number_format($netProfitAccrual, 0, ',', '.') }}đ</b>
            </div>
            <div class="small text-muted mt-1">
                Tỷ suất lợi nhuận trên doanh thu: <b>{{ $totalInvoiced > 0 ? round(($netProfitAccrual / $totalInvoiced) * 100, 1) : 0 }}%</b>
            </div>
        </div>
    </div>
</div>

<!-- BẢNG ĐỐI SOÁT CHI TIẾT THU VÀ CHI -->
<div class="row g-4 mb-4">
    <!-- Chi tiết Dòng Thu -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success-subtle text-success-emphasis fw-bold">
                <i class="bi bi-arrow-down-left-circle me-1"></i> CHI TIẾT DÒNG THU (TỪ KHÁCH THUÊ)
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        <tr>
                            <td>1. Tiền thuê phòng trọ</td>
                            <td class="text-end fw-bold">{{ number_format($revenueRoom, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>2. Tiền điện thu các phòng lẻ</td>
                            <td class="text-end fw-bold text-warning-emphasis">{{ number_format($revenueElec, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>3. Tiền nước thu các phòng lẻ</td>
                            <td class="text-end fw-bold text-info-emphasis">{{ number_format($revenueWater, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>4. Tiền dịch vụ (Wifi, rác, xe máy, thang máy)</td>
                            <td class="text-end fw-bold">{{ number_format($revenueServices, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr class="table-light fs-6 fw-bold">
                            <td>TỔNG DOANH THU PHÁT SINH</td>
                            <td class="text-end text-success">{{ number_format($totalInvoiced, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Thực tế đã thu:</td>
                            <td class="text-end fw-bold text-success">{{ number_format($totalCollected, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr class="table-danger">
                            <td class="text-danger fw-bold">Còn nợ đọng chưa thu:</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($totalDebt, 0, ',', '.') }}đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chi tiết Dòng Chi -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-danger-subtle text-danger-emphasis fw-bold">
                <i class="bi bi-arrow-up-right-circle me-1"></i> CHI TIẾT DÒNG CHI (TRẢ NHÀ NƯỚC & VẬN HÀNH)
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        <tr>
                            <td>1. Hóa đơn Điện tổng trả EVN (Nhà nước)</td>
                            <td class="text-end fw-bold text-warning-emphasis">{{ number_format($expenseElecEvn, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>2. Hóa đơn Nước tổng trả Công ty Cấp nước</td>
                            <td class="text-end fw-bold text-info-emphasis">{{ number_format($expenseWaterSupply, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>3. Thuế môn bài / Thuế kinh doanh nộp Nhà nước</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($expenseTax, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>4. Cáp quang Internet tổng tòa nhà</td>
                            <td class="text-end fw-bold">{{ number_format($expenseInternet, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>5. Phí rác thải môi trường dân sinh</td>
                            <td class="text-end fw-bold">{{ number_format($expenseWaste, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td>6. Bảo trì thang máy, máy bơm & sửa chữa chung</td>
                            <td class="text-end fw-bold">{{ number_format($expenseMaintenance, 0, ',', '.') }}đ</td>
                        </tr>
                        @if($expenseOther > 0)
                            <tr>
                                <td>7. Chi phí phát sinh khác</td>
                                <td class="text-end fw-bold">{{ number_format($expenseOther, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif
                        <tr class="table-light fs-6 fw-bold">
                            <td>TỔNG CHI PHÍ THỰC TẾ</td>
                            <td class="text-end text-danger">{{ number_format($totalExpenses, 0, ',', '.') }}đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ĐỐI SOÁT HAO HỤT ĐIỆN NƯỚC TỔNG VS CON -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold text-dark">
        <i class="bi bi-shield-exclamation text-warning me-2"></i>Đối Soát Hao Hụt Điện & Nước (Đồng Hồ Tổng vs Tổng Các Phòng Con)
    </div>
    <div class="card-body">
        <div class="row g-4">
            <!-- Điện -->
            <div class="col-12 col-md-6 border-end">
                <h6 class="fw-bold text-dark mb-2">⚡ Đối soát Điện lực (EVN):</h6>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Chỉ số điện trên hóa đơn EVN tổng:</span>
                    <span class="fw-bold">{{ number_format($masterElecKwh, 1) }} kWh</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Tổng số điện thu từ các phòng lẻ:</span>
                    <span class="fw-bold text-primary">{{ number_format($subRoomsElecKwh, 1) }} kWh</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Điện thắp sáng hành lang, máy bơm & hao hụt:</span>
                    <span class="fw-bold text-warning-emphasis">+{{ number_format($elecDiff, 1) }} kWh ({{ $elecLossRate }}%)</span>
                </div>
                <div class="small text-muted mt-2">
                    Tiền điện thu phòng lẻ: <b>{{ number_format($revenueElec) }}đ</b> vs Tiền điện trả EVN: <b>{{ number_format($expenseElecEvn) }}đ</b>
                    (Chênh lệch: <b class="{{ ($revenueElec - $expenseElecEvn) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($revenueElec - $expenseElecEvn) }}đ</b>).
                </div>
            </div>

            <!-- Nước -->
            <div class="col-12 col-md-6">
                <h6 class="fw-bold text-dark mb-2">💧 Đối soát Nước sinh hoạt:</h6>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Chỉ số nước trên hóa đơn Cấp nước tổng:</span>
                    <span class="fw-bold">{{ number_format($masterWaterM3, 1) }} m³</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Tổng số nước thu từ các phòng lẻ:</span>
                    <span class="fw-bold text-info">{{ number_format($subRoomsWaterM3, 1) }} m³</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Nước thất thoát / sân chung / máy giặt:</span>
                    <span class="fw-bold text-danger">+{{ number_format($waterDiff, 1) }} m³ ({{ $waterLossRate }}%)</span>
                </div>
                <div class="small text-muted mt-2">
                    Tiền nước thu phòng lẻ: <b>{{ number_format($revenueWater) }}đ</b> vs Tiền nước trả Nhà máy: <b>{{ number_format($expenseWaterSupply) }}đ</b>
                    (Chênh lệch: <b class="{{ ($revenueWater - $expenseWaterSupply) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($revenueWater - $expenseWaterSupply) }}đ</b>).
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BẢNG THEO DÕI DÒNG TIỀN CẢ 12 THÁNG TRONG NĂM -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold text-dark">
        <i class="bi bi-calendar3 me-2 text-primary"></i>Tổng Hợp Dòng Tiền & Lợi Nhuận Cả Năm {{ $year }}
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0 text-center small">
            <thead class="table-light">
                <tr>
                    <th>Tháng</th>
                    @for($m = 1; $m <= 12; $m++)
                        <th class="{{ $m == $month ? 'table-primary fw-bold' : '' }}">T{{ $m }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-start text-success">Doanh thu thu về</td>
                    @foreach($yearlyStats as $s)
                        <td class="{{ $s['month'] == $month ? 'table-primary' : '' }} fw-semibold text-success">
                            {{ $s['revenue'] > 0 ? number_format($s['revenue'] / 1000000, 1) . 'tr' : '-' }}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <td class="fw-bold text-start text-danger">Chi phí trả đi</td>
                    @foreach($yearlyStats as $s)
                        <td class="{{ $s['month'] == $month ? 'table-primary' : '' }} fw-semibold text-danger">
                            {{ $s['expense'] > 0 ? number_format($s['expense'] / 1000000, 1) . 'tr' : '-' }}
                        </td>
                    @endforeach
                </tr>
                <tr class="table-light fs-6">
                    <td class="fw-bold text-start text-primary">Lợi nhuận ròng</td>
                    @foreach($yearlyStats as $s)
                        <td class="{{ $s['month'] == $month ? 'table-primary' : '' }} fw-bold {{ $s['profit'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ $s['profit'] != 0 ? number_format($s['profit'] / 1000000, 1) . 'tr' : '-' }}
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
