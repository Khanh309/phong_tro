@extends('layouts.app')

@section('title', 'Tổng quan Quản lý Nhà Trọ')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Bảng Điều Khiển Trung Tâm</h3>
        <p class="text-muted mb-0">Theo dõi dòng tiền thu chi, công tơ điện nước và cảnh báo đến hạn nộp tiền</p>
    </div>

    <!-- BỘ LỌC CƠ SỞ & THỜI GIAN + NÚT THÊM NHÀ TRỌ -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('properties.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="bi bi-building-add me-1"></i> + Thêm Nhà Trọ Mới
            </a>
        @endif

        <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center m-0">
        @if($isManager)
            <div class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 fs-6">
                📍 {{ $properties->first()->name ?? 'Cơ sở của bạn' }}
            </div>
            <input type="hidden" name="property_id" value="{{ $propertyId }}">
        @else
            <select name="property_id" class="form-select form-select-sm" style="min-width: 220px;" onchange="this.form.submit()">
                <option value="">🏢 Tất cả các Nhà trọ ({{ $properties->count() }} cơ sở)</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>
                        📍 {{ $prop->name }}
                    </option>
                @endforeach
            </select>
        @endif

        <select name="month" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
            @endfor
        </select>

        <select name="year" class="form-select form-select-sm" style="width: 100px;" onchange="this.form.submit()">
            @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
            @endfor
        </select>
    </form>
    </div>
</div>

<!-- TÁC VỤ NHANH DÀNH CHO CHỦ NHÀ & QUẢN LÝ (DỄ DÙNG & TIỆN LỢI NHẤT) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
    <div class="col">
        <a href="{{ route('properties.index') }}" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-dark text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-white text-dark rounded-3 fs-3">
                    <i class="bi bi-buildings-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">1. Nhà Trọ / Cơ Sở</h6>
                    <small class="text-white-50">{{ $properties->count() }} Tòa nhà đang quản lý</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('invoices.bulk_create', ['property_id' => $propertyId, 'month' => $month, 'year' => $year]) }}" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-warning text-dark">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-dark text-warning rounded-3 fs-3">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">2. Chốt Điện Nước</h6>
                    <small class="text-dark-50">Ghi chỉ số 1 phút cả dãy</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('rooms.index', ['property_id' => $propertyId]) }}" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-primary text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-white text-primary rounded-3 fs-3">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">3. Sơ Đồ Phòng Trọ</h6>
                    <small class="text-white-50">Xem phòng trống & dịch vụ</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('contracts.create') }}" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-success text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-white text-success rounded-3 fs-3">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">4. Khách Thuê Mới</h6>
                    <small class="text-white-50">Lập hợp đồng & nhận cọc</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="{{ route('portal.index') }}" target="_blank" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-info text-dark">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-dark text-info rounded-3 fs-3">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">5. Cổng Khách Thuê</h6>
                    <small class="text-dark-50">Xem HĐ, quét QR, khiếu nại</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- THẺ KPI TÀI CHÍNH & TÌNH TRẠNG PHÒNG -->
<div class="row g-3 mb-4">
    <!-- Tỷ lệ lấp đầy phòng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold">TỶ LỆ LẤP ĐẦY</span>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">{{ $occupancyRate }}%</span>
            </div>
            <div class="d-flex align-items-baseline gap-2 mb-2">
                <h2 class="fw-bold mb-0">{{ $occupiedRooms }}</h2>
                <span class="text-muted">/ {{ $totalRooms }} phòng đang thuê</span>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $occupancyRate }}%;"></div>
            </div>
            <div class="d-flex justify-content-between text-muted small mt-2">
                <span>Trống: <b class="text-success">{{ $availableRooms }}</b></span>
                <span>Sửa/Dọn: <b class="text-warning">{{ $maintenanceRooms }}</b></span>
            </div>
        </div>
    </div>

    <!-- DÒNG THU: Tiền khách thuê -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold">DOANH THU THU KHÁCH</span>
                <i class="bi bi-wallet2 text-success fs-5"></i>
            </div>
            <h3 class="fw-bold text-success mb-1">{{ number_format($totalCollected, 0, ',', '.') }}đ</h3>
            <div class="text-muted small">
                Tổng hóa đơn: <b>{{ number_format($totalInvoiceAmount, 0, ',', '.') }}đ</b>
            </div>
            <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                <span class="text-danger fw-semibold">Chưa thu (Nợ):</span>
                <span class="text-danger fw-bold">{{ number_format($totalUnpaid, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>

    <!-- DÒNG CHI: Trả Nhà nước & Vận hành -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold">CHI PHÍ TRẢ NHÀ NƯỚC & VH</span>
                <i class="bi bi-bank text-danger fs-5"></i>
            </div>
            <h3 class="fw-bold text-danger mb-1">{{ number_format($totalExpenses, 0, ',', '.') }}đ</h3>
            <div class="text-muted small">
                Điện EVN: <b>{{ number_format($expenseElecEvn, 0, ',', '.') }}đ</b> | Thuế: <b>{{ number_format($expenseStateTax, 0, ',', '.') }}đ</b>
            </div>
            <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                <span class="text-muted">Nước tổng & Phí khác:</span>
                <span class="fw-semibold">{{ number_format($expenseWaterSupply + $expenseOther, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>

    <!-- LỢI NHUẬN RÒNG (THỰC THU - THỰC CHI) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3 {{ $netProfit >= 0 ? 'border-success' : 'border-danger' }}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted small fw-semibold">LỢI NHUẬN RÒNG (THU - CHI)</span>
                <i class="bi bi-cash-coin {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} fs-5"></i>
            </div>
            <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                {{ ($netProfit >= 0 ? '+' : '') . number_format($netProfit, 0, ',', '.') }}đ
            </h3>
            <div class="text-muted small">
                {{ $netProfit >= 0 ? 'Dòng tiền dương sau khi chi trả' : 'Chưa thu đủ tiền bù chi phí' }}
            </div>
            <div class="mt-2 pt-2 border-top d-flex justify-content-between small">
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('reports.financial', ['property_id' => $propertyId, 'month' => $month, 'year' => $year]) }}" class="text-decoration-none fw-semibold">
                        Xem báo cáo chi tiết <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <span class="text-muted small fst-italic"><i class="bi bi-shield-lock me-1"></i> Báo cáo tài chính: Chỉ Chủ trọ xem</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- DANH SÁCH NHÀ TRỌ / TÒA NHÀ CỦA BẠN -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-buildings-fill text-primary me-2"></i>
                Danh Sách Nhà Trọ / Tòa Nhà ({{ $properties->count() }} Cơ sở)
            </h5>
            <small class="text-muted">Quản lý các tòa nhà trọ, căn hộ và thông tin vận hành</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-grid-fill me-1"></i> Xem tất cả nhà trọ
            </a>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('properties.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Thêm Nhà Trọ Mới
                </a>
            @endif
        </div>
    </div>
    <div class="card-body p-4 bg-light-subtle">
        <div class="row g-4">
            @forelse($properties as $prop)
                @php
                    $propRoomsCount = $prop->rooms_count ?? $prop->rooms->count();
                    $propOccupied = $prop->occupied_rooms_count ?? $prop->rooms->where('status', 'occupied')->count();
                    $propAvailable = $prop->available_rooms_count ?? $prop->rooms->where('status', 'available')->count();
                    $propOccRate = $propRoomsCount > 0 ? round(($propOccupied / $propRoomsCount) * 100, 1) : 0;
                @endphp
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary mb-1">
                                        <i class="bi bi-building me-1"></i> Tòa nhà {{ $prop->total_floors }} tầng
                                    </span>
                                    <h5 class="card-title fw-bold text-dark mb-1">
                                        <a href="{{ route('properties.show', $prop->id) }}" class="text-dark text-decoration-none">
                                            🏢 {{ $prop->name }}
                                        </a>
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $prop->address }}, {{ $prop->district }}, {{ $prop->city }}
                                    </p>
                                </div>
                                <span class="badge bg-{{ $propOccRate >= 80 ? 'success' : ($propOccRate >= 50 ? 'primary' : 'warning') }}-subtle text-{{ $propOccRate >= 80 ? 'success' : ($propOccRate >= 50 ? 'primary' : 'warning') }} fs-6 px-3 py-2 rounded-pill">
                                    {{ $propOccRate }}% Lấp đầy
                                </span>
                            </div>

                            <!-- Thống kê phòng -->
                            <div class="bg-light p-3 rounded-3 my-3">
                                <div class="row text-center g-2">
                                    <div class="col-4 border-end">
                                        <small class="text-muted d-block">Tổng phòng</small>
                                        <b class="fs-5 text-dark">{{ $propRoomsCount }}</b>
                                    </div>
                                    <div class="col-4 border-end">
                                        <small class="text-muted d-block">Đang thuê</small>
                                        <b class="fs-5 text-primary">{{ $propOccupied }}</b>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Còn trống</small>
                                        <b class="fs-5 text-success">{{ $propAvailable }}</b>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $propOccRate }}%;"></div>
                                </div>
                            </div>

                            <!-- Thông tin VietQR & Điện Nước -->
                            <div class="small text-muted mb-3 border-bottom pb-3">
                                @if($prop->bank_name && $prop->bank_account_number)
                                    <div class="mb-1">
                                        <i class="bi bi-credit-card-2-front-fill text-success me-1"></i>
                                        <b>VietQR:</b> {{ $prop->bank_name }} - <code>{{ $prop->bank_account_number }}</code> ({{ $prop->bank_account_holder }})
                                    </div>
                                @endif
                                @if($prop->electricity_meter_code)
                                    <div>
                                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>
                                        <b>Mã EVN:</b> <code>{{ $prop->electricity_meter_code }}</code>
                                        @if($prop->water_meter_code)
                                            <span class="ms-2"><i class="bi bi-droplet-fill text-info me-1"></i> <b>Nước:</b> <code>{{ $prop->water_meter_code }}</code></span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Nút thao tác -->
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('properties.show', $prop->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bi bi-info-circle me-1"></i> Chi tiết nhà trọ
                                </a>
                                <a href="{{ route('rooms.index', ['property_id' => $prop->id]) }}" class="btn btn-outline-secondary btn-sm" title="Sơ đồ phòng tòa này">
                                    <i class="bi bi-door-open me-1"></i> Sơ đồ {{ $propRoomsCount }} phòng
                                </a>
                                <a href="{{ route('invoices.bulk_create', ['property_id' => $prop->id, 'month' => $month, 'year' => $year]) }}" class="btn btn-outline-warning text-dark btn-sm" title="Chốt điện nước nhà này">
                                    <i class="bi bi-lightning-charge me-1"></i> Chốt điện nước
                                </a>
                                @if(auth()->user()?->isAdmin())
                                    <a href="{{ route('properties.edit', $prop->id) }}" class="btn btn-outline-dark btn-sm" title="Sửa thông tin">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">Chưa có cơ sở nhà trọ nào được phân công.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- KHU VỰC CẢNH BÁO: HÓA ĐƠN QUÁ HẠN & SẮP ĐẾN HẠN -->
<div class="row g-4 mb-4">
    <!-- Hóa đơn quá hạn (Đỏ) -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-danger">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    <span class="fw-bold">CẢNH BÁO: HÓA ĐƠN QUÁ HẠN NỘP TIỀN</span>
                </div>
                <span class="badge bg-white text-danger fw-bold">{{ $overdueInvoices->count() }} phòng</span>
            </div>
            <div class="card-body p-0">
                @if($overdueInvoices->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                        Tuyệt vời! Không có phòng nào bị nợ tiền quá hạn.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Phòng</th>
                                    <th>Khách thuê</th>
                                    <th>Tiền nợ</th>
                                    <th>Hạn nộp</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueInvoices as $inv)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoices.show', $inv->id) }}" class="text-decoration-none">
                                                <span class="badge bg-dark font-monospace"><i class="bi bi-receipt me-1"></i>{{ $inv->invoice_code }}</span>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">P.{{ $inv->room->room_number }}</span>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $inv->room->property->name }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $inv->contract->tenant->name ?? '---' }}</div>
                                            <div class="text-muted small">{{ $inv->contract->tenant->phone ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-bold">{{ number_format($inv->remaining_amount, 0, ',', '.') }}đ</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger">
                                                {{ $inv->due_date->format('d/m/Y') }} (Trễ {{ (int) now()->diffInDays($inv->due_date) }} ngày)
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="copyZaloModal('{{ addslashes($inv->generateZaloMessage()) }}', '{{ $inv->contract->tenant->phone ?? '' }}')" title="Nhắc nợ qua Zalo">
                                                    <i class="bi bi-chat-dots-fill"></i> Zalo
                                                </button>
                                                <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary" title="Xem hóa đơn">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hóa đơn sắp đến hạn & Hợp đồng sắp hết hạn -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-warning-subtle text-warning-emphasis d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-5"></i>
                    <span class="fw-bold">HÓA ĐƠN SẮP ĐẾN HẠN (3 NGÀY TỚI)</span>
                </div>
                <span class="badge bg-warning text-dark">{{ $dueSoonInvoices->count() }} phòng</span>
            </div>
            <div class="card-body p-0">
                @if($dueSoonInvoices->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar2-check text-muted fs-2 mb-2 d-block"></i>
                        Không có hóa đơn nào sắp đến hạn trong 3 ngày tới.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Phòng</th>
                                    <th>Khách thuê</th>
                                    <th>Số tiền</th>
                                    <th>Hạn nộp</th>
                                    <th>Nhắc nợ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dueSoonInvoices as $inv)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoices.show', $inv->id) }}" class="text-decoration-none">
                                                <span class="badge bg-dark font-monospace"><i class="bi bi-receipt me-1"></i>{{ $inv->invoice_code }}</span>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-bold">P.{{ $inv->room->room_number }}</span>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $inv->room->property->name }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $inv->contract->tenant->name ?? '---' }}</div>
                                            <div class="text-muted small">{{ $inv->contract->tenant->phone ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ number_format($inv->remaining_amount, 0, ',', '.') }}đ</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning-subtle text-dark">
                                                {{ $inv->due_date->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="copyZaloModal('{{ addslashes($inv->generateZaloMessage()) }}', '{{ $inv->contract->tenant->phone ?? '' }}')">
                                                    <i class="bi bi-chat-dots"></i> Nhắc Zalo
                                                </button>
                                                <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary" title="Xem hóa đơn">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if($expiringContracts->isNotEmpty())
                <div class="card-footer bg-light border-top p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-file-earmark-break-fill text-primary"></i>
                        <span class="fw-bold small text-uppercase">Hợp đồng sắp hết hạn ({{ $expiringContracts->count() }} phòng):</span>
                    </div>
                    <ul class="list-unstyled mb-0 small">
                        @foreach($expiringContracts as $ctr)
                            <li class="d-flex justify-content-between py-1 border-bottom border-light">
                                <span><b>P.{{ $ctr->room->room_number }}</b> - {{ $ctr->tenant->name }} ({{ $ctr->room->property->name }})</span>
                                <span class="text-danger fw-semibold">Hết hạn: {{ $ctr->end_date->format('d/m/Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- KHU VỰC Ý KIẾN / KHIẾU NẠI HÓA ĐƠN TỪ KHÁCH THUÊ -->
<div class="card mb-4 shadow-sm border-info">
    <div class="card-header bg-info-subtle text-info-emphasis d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-chat-left-dots-fill fs-5"></i>
            <span class="fw-bold">Ý KIẾN / KHIẾU NẠI HÓA ĐƠN TỪ KHÁCH THUÊ</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-info text-dark fw-bold">{{ $feedbacks->count() }} phản hồi gần nhất</span>
            <a href="{{ route('portal.index') }}" target="_blank" class="btn btn-sm btn-outline-info bg-white text-dark">
                <i class="bi bi-person-badge me-1"></i> Xem Cổng Khách Thuê
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($feedbacks->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="bi bi-emoji-smile fs-2 text-success d-block mb-1"></i>
                Hiện tại không có khiếu nại hay thắc mắc nào từ người thuê trọ.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Mã Hóa Đơn</th>
                            <th>Phòng & Khách Thuê</th>
                            <th>Nội Dung Thắc Mắc</th>
                            <th>Chi Tiết Ý Kiến Của Khách</th>
                            <th>Trạng Thái</th>
                            <th>Phản Hồi Của Chủ Nhà</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feedbacks as $fb)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $fb->invoice->id) }}">
                                        <span class="badge bg-dark font-monospace fs-6"><i class="bi bi-receipt me-1"></i>{{ $fb->invoice->invoice_code }}</span>
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-bold">P.{{ $fb->invoice->room->room_number }} - {{ $fb->tenant->name }}</div>
                                    <div class="text-muted small">{{ $fb->invoice->room->property->name }} | {{ $fb->tenant->phone }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ $fb->feedback_type_name }}</span>
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-dark small fw-semibold" title="{{ $fb->content }}">
                                        "{{ $fb->content }}"
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $fb->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $fb->status_badge }}-subtle text-{{ $fb->status_badge }} fw-bold">
                                        {{ $fb->status_label }}
                                    </span>
                                </td>
                                <td style="max-width: 250px;">
                                    @if($fb->admin_reply)
                                        <div class="small text-success fw-semibold" title="{{ $fb->admin_reply }}">
                                            <i class="bi bi-reply-fill"></i> {{ $fb->admin_reply }}
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Chưa có phản hồi</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openReplyFeedbackModal('{{ $fb->id }}', '{{ $fb->invoice->invoice_code }}', '{{ addslashes($fb->content) }}', '{{ addslashes($fb->admin_reply ?? '') }}', '{{ $fb->status }}')">
                                        <i class="bi bi-reply-fill me-1"></i> Phản hồi
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- ĐỐI SOÁT HAO HỤT ĐIỆN NƯỚC (ĐỒNG HỒ TỔNG VS TỔNG PHÒNG LẺ) -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Đối Soát Điện (EVN Tổng vs Phòng Lẻ)</span>
                @if($totalMasterElecKwh > 0 && $elecLeakKwh > 0)
                    <span class="badge bg-warning text-dark">Chênh lệch: +{{ $elecLeakKwh }} kWh</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="text-muted small">Đồng hồ Tổng EVN</div>
                        <h4 class="fw-bold text-dark mt-1">{{ number_format($totalMasterElecKwh, 1) }} <small class="fs-6 text-muted">kWh</small></h4>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Tổng các phòng lẻ tiêu thụ</div>
                        <h4 class="fw-bold text-primary mt-1">{{ number_format($totalSubRoomsElecKwh, 1) }} <small class="fs-6 text-muted">kWh</small></h4>
                    </div>
                </div>

                @if($totalMasterElecKwh > 0)
                    <div class="alert alert-light border small mb-0">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Lượng điện hành lang, bơm nước & hao hụt: <b>{{ number_format($elecLeakKwh, 1) }} kWh</b>
                        ({{ round(($elecLeakKwh / $totalMasterElecKwh) * 100, 1) }}% tổng điện tòa nhà).
                    </div>
                @else
                    <div class="text-muted small text-center">Chưa nhập chỉ số hóa đơn điện tổng EVN tháng này.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-droplet-fill text-info me-1"></i> Đối Soát Nước (Cấp Nước Tổng vs Phòng Lẻ)</span>
                @if($totalMasterWaterM3 > 0 && $waterLeakM3 > 0)
                    <span class="badge bg-info text-dark">Chênh lệch: +{{ $waterLeakM3 }} m³</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="text-muted small">Đồng hồ Nước Tổng</div>
                        <h4 class="fw-bold text-dark mt-1">{{ number_format($totalMasterWaterM3, 1) }} <small class="fs-6 text-muted">m³</small></h4>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Tổng các phòng lẻ tiêu thụ</div>
                        <h4 class="fw-bold text-info mt-1">{{ number_format($totalSubRoomsWaterM3, 1) }} <small class="fs-6 text-muted">m³</small></h4>
                    </div>
                </div>

                @if($totalMasterWaterM3 > 0)
                    <div class="alert alert-light border small mb-0">
                        <i class="bi bi-info-circle text-info me-1"></i>
                        Lượng nước hao hụt / sân chung: <b>{{ number_format($waterLeakM3, 1) }} m³</b>
                        ({{ round(($waterLeakM3 / $totalMasterWaterM3) * 100, 1) }}%).
                        @if($waterLeakM3 / $totalMasterWaterM3 > 0.15)
                            <div class="text-danger mt-1 fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Cảnh báo: Tỷ lệ thất thoát nước cao > 15%, nên kiểm tra rò rỉ đường ống hoặc phao bể tràn!</div>
                        @endif
                    </div>
                @else
                    <div class="text-muted small text-center">Chưa nhập chỉ số hóa đơn nước tổng tháng này.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL XEM & COPY TIN NHẮN ZALO NHẮC NỢ -->
<div class="modal fade" id="zaloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-chat-dots-fill me-2"></i> Mẫu Tin Nhắn Zalo Nhắc Nộp Tiền</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Nội dung tin nhắn đã được soạn sẵn đầy đủ tiền phòng, số điện nước, STK ngân hàng:</p>
                <textarea id="zaloMessageContent" class="form-control" rows="10" readonly style="font-family: monospace; font-size: 0.88rem;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <a id="openZaloBtn" href="#" target="_blank" class="btn btn-outline-success">
                    <i class="bi bi-send-fill me-1"></i> Mở Zalo Web
                </a>
                <button type="button" class="btn btn-success" onclick="copyZaloContent()">
                    <i class="bi bi-clipboard-check me-1"></i> Sao chép tin nhắn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PHẢN HỒI KHIẾU NẠI CỦA KHÁCH THUÊ -->
<div class="modal fade" id="replyFeedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="replyFeedbackForm" action="" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-reply-fill me-2"></i>Phản Hồi Khiếu Nại Của Khách Thuê</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border small mb-3">
                        <div>Hóa đơn: <b id="rfModalInvoiceCode" class="font-monospace text-primary"></b></div>
                        <div class="mt-1">Khách viết: <i id="rfModalContent" class="text-dark fw-semibold"></i></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái xử lý: <span class="text-danger">*</span></label>
                        <select name="status" id="rfModalStatus" class="form-select" required>
                            <option value="processing">⏳ Đang kiểm tra / Đang xử lý</option>
                            <option value="resolved">✅ Đã giải quyết / Đã chốt thống nhất</option>
                            <option value="rejected">❌ Từ chối khiếu nại (Chỉ số đúng)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lời nhắn phản hồi gửi khách: <span class="text-danger">*</span></label>
                        <textarea name="admin_reply" id="rfModalReply" class="form-control" rows="4" required placeholder="Nhập lời giải thích hoặc phương án điều chỉnh cho khách thuê..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Lưu & Gửi Phản Hồi Cho Khách
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openReplyFeedbackModal(id, invoiceCode, content, reply, status) {
        document.getElementById('replyFeedbackForm').action = '/admin/feedbacks/' + id + '/reply';
        document.getElementById('rfModalInvoiceCode').textContent = invoiceCode;
        document.getElementById('rfModalContent').textContent = '"' + content + '"';
        document.getElementById('rfModalReply').value = reply || '';
        document.getElementById('rfModalStatus').value = status || 'resolved';
        var modal = new bootstrap.Modal(document.getElementById('replyFeedbackModal'));
        modal.show();
    }

    function copyZaloModal(msg, phone) {
        document.getElementById('zaloMessageContent').value = msg;
        if (phone) {
            document.getElementById('openZaloBtn').href = 'https://zalo.me/' + phone.replace(/[^0-9]/g, '');
        } else {
            document.getElementById('openZaloBtn').href = 'https://chat.zalo.me/';
        }
        var modal = new bootstrap.Modal(document.getElementById('zaloModal'));
        modal.show();
    }

    function copyZaloContent() {
        var textarea = document.getElementById('zaloMessageContent');
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(textarea.value).then(function() {
            alert('Đã sao chép nội dung tin nhắn nhắc nợ vào bộ nhớ tạm!');
        });
    }
</script>
@endpush
