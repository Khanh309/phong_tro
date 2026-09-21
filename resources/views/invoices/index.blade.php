@extends('layouts.app')

@section('title', 'Quản Lý Hóa Đơn & Bảng Kê Chi Tiết Phí Từng Phòng Theo Tháng')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Quản Lý Hóa Đơn Phòng & Bảng Kê Chi Tiết Phí Theo Tháng</h3>
        <p class="text-muted mb-0">Theo dõi chi tiết các khoản tiền phòng, điện, nước và dịch vụ phát sinh hàng tháng theo từng phòng và từng khách ở cùng</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.bulk_create', ['month' => $month, 'year' => $year, 'property_id' => $propertyId]) }}" class="btn btn-warning text-dark fw-bold">
            <i class="bi bi-lightning-charge-fill me-1"></i> Chốt Điện Nước & Lập Hóa Đơn Cả Dãy
        </a>
        <a href="{{ route('invoices.create', ['month' => $month, 'year' => $year]) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Lập Hóa Đơn Phòng Lẻ
        </a>
    </div>
</div>

<!-- BỘ LỌC HÓA ĐƠN THEO THÁNG & CƠ SỞ -->
<div class="card p-3 mb-4 shadow-sm border-0">
    <form method="GET" action="{{ route('invoices.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Cơ sở / Tòa nhà:</label>
            <select name="property_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">🏢 Tất cả các Nhà trọ ({{ $properties->count() }} cơ sở)</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ $propertyId == $p->id ? 'selected' : '' }}>📍 {{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Kỳ Tháng:</label>
            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Năm:</label>
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Trạng thái:</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="partially_paid" {{ $status == 'partially_paid' ? 'selected' : '' }}>Trả một phần</option>
                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Đã thanh toán đủ</option>
                <option value="overdue" {{ $status == 'overdue' ? 'selected' : '' }}>Quá hạn nộp tiền</option>
            </select>
        </div>
        <div class="col-6 col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Lọc Dữ Liệu</button>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">Đặt lại</a>
        </div>
    </form>
</div>

<!-- TỔNG KẾT TÀI CHÍNH THÁNG NÀY -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card p-3 border-start border-primary border-4 shadow-sm">
            <div class="text-muted small fw-semibold">TỔNG TIỀN PHẢI THU THÁNG {{ $month }}/{{ $year }}</div>
            <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalAmount, 0, ',', '.') }}đ</h3>
            <small class="text-muted">Tổng tiền từ {{ $invoices->count() }} phòng có hóa đơn kỳ này</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 border-start border-success border-4 shadow-sm">
            <div class="text-muted small fw-semibold">THỰC TẾ ĐÃ THU VỀ</div>
            <h3 class="fw-bold text-success mt-1 mb-0">{{ number_format($totalPaid, 0, ',', '.') }}đ</h3>
            <small class="text-success fw-semibold">Tỷ lệ thu: {{ $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 1) : 0 }}%</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 border-start border-danger border-4 shadow-sm">
            <div class="text-muted small fw-semibold">CÒN NỢ CHƯA THU</div>
            <h3 class="fw-bold text-danger mt-1 mb-0">{{ number_format($totalRemaining, 0, ',', '.') }}đ</h3>
            <small class="text-danger">Cần đôn đốc nhắc nhở thanh toán</small>
        </div>
    </div>
</div>

<!-- BẢNG KÊ CHI TIẾT PHÍ TỪNG PHÒNG -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark fs-5">
            <i class="bi bi-receipt text-primary me-2"></i>Chi Tiết Phí Từng Phòng Tháng {{ $month }}/{{ $year }}
        </span>
        <span class="badge bg-primary fs-6">{{ $invoices->count() }} hóa đơn</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 140px;">Phòng & Cơ Sở</th>
                    <th style="min-width: 220px;">Tất Cả Khách Thuê Trong Phòng (Ở ghép / Ở cùng)</th>
                    <th class="text-end" style="width: 120px;">Tiền Thuê Phòng</th>
                    <th style="min-width: 180px;">⚡ Tiền Điện Tháng Này</th>
                    <th style="min-width: 170px;">💧 Tiền Nước Tháng Này</th>
                    <th style="min-width: 260px;">Chi Tiết Các Khoản Phí Dịch Vụ (Wifi, Rác, Xe, Thang máy...)</th>
                    <th class="text-end" style="width: 130px;">Tổng Phải Thu</th>
                    <th class="text-end" style="width: 120px;">Đã Thu / Còn Nợ</th>
                    <th class="text-center" style="width: 120px;">Trạng Thái</th>
                    <th class="text-center" style="width: 100px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    @php
                        $contract = $inv->contract;
                        $members = $contract ? $contract->members : collect();
                        $totalTenants = $contract ? (1 + $members->count()) : 1;
                    @endphp
                    <tr>
                        <!-- 1. Phòng & Cơ Sở -->
                        <td>
                            <a href="{{ route('invoices.show', $inv->id) }}" class="fw-bold text-dark text-decoration-none fs-6">
                                Phòng {{ $inv->room->room_number }}
                            </a>
                            <div class="text-muted small">{{ $inv->room->property->name }}</div>
                            <div class="mt-1">
                                <span class="badge bg-dark font-monospace" style="font-size: 0.7rem;">{{ $inv->invoice_code }}</span>
                            </div>
                            <div class="mt-1 small text-primary fw-semibold">
                                <i class="bi bi-people-fill"></i> Đang ở: {{ $totalTenants }}/{{ $inv->room->max_tenants }} người
                            </div>
                        </td>

                        <!-- 2. Tất cả khách thuê trong phòng (Người đại diện + Các thành viên ở cùng) -->
                        <td>
                            @if($contract && $contract->tenant)
                                <div class="p-2 rounded bg-light border small">
                                    <!-- Người đại diện ký HĐ -->
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="fw-bold text-dark">
                                            <i class="bi bi-person-fill-check text-success me-1"></i>{{ $contract->tenant->name }}
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary" style="font-size: 0.7rem;">Đại diện HĐ</span>
                                    </div>
                                    <div class="text-muted mb-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-telephone me-1"></i>{{ $contract->tenant->phone }}
                                        @if($contract->tenant->vehicle_plate)
                                            | <i class="bi bi-bicycle me-1"></i>Xe: {{ $contract->tenant->vehicle_plate }}
                                        @endif
                                    </div>

                                    <!-- Các thành viên ở cùng phòng -->
                                    @if($members->isNotEmpty())
                                        <div class="pt-1 border-top">
                                            <div class="text-muted fw-bold mb-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-people me-1"></i>Người ở cùng / ở ghép ({{ $members->count() }}):
                                            </div>
                                            @foreach($members as $m)
                                                <div class="d-flex align-items-center justify-content-between text-dark py-0" style="font-size: 0.75rem;">
                                                    <span>• {{ $m->name }} <span class="text-muted">({{ $m->relationship ?: 'ở cùng' }})</span></span>
                                                    <span class="text-muted">{{ $m->phone ?: ($m->vehicle_plate ? 'Xe: '.$m->vehicle_plate : '') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="pt-1 border-top text-muted fst-italic" style="font-size: 0.72rem;">
                                            (Khách ở 1 mình, chưa khai báo người ở cùng)
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Không có hợp đồng</span>
                            @endif
                        </td>

                        <!-- 3. Tiền thuê phòng -->
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($inv->room_price, 0, ',', '.') }}đ
                        </td>

                        <!-- 4. Tiền điện tháng này -->
                        <td>
                            <div class="fw-bold text-warning-emphasis fs-6">
                                {{ number_format($inv->electricity_total, 0, ',', '.') }}đ
                            </div>
                            <div class="small text-muted font-monospace" style="font-size: 0.78rem;">
                                {{ $inv->electricity_old }} → {{ $inv->electricity_new }}
                            </div>
                            <div class="small text-dark fw-semibold" style="font-size: 0.75rem;">
                                Dùng: <b>{{ $inv->electricity_usage }} kWh</b> × {{ number_format($inv->electricity_rate, 0, ',', '.') }}đ
                            </div>
                        </td>

                        <!-- 5. Tiền nước tháng này -->
                        <td>
                            <div class="fw-bold text-info-emphasis fs-6">
                                {{ number_format($inv->water_total, 0, ',', '.') }}đ
                            </div>
                            @php
                                $wType = $inv->water_calculation_type ?? $inv->room->water_calculation_type;
                            @endphp
                            @if($wType === 'per_person')
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    <span class="badge bg-info-subtle text-info border">👥 Theo người</span>
                                </div>
                                <div class="small text-dark fw-semibold mt-1" style="font-size: 0.75rem;">
                                    <b>{{ $inv->water_usage }} người</b> × {{ number_format($inv->water_rate, 0, ',', '.') }}đ
                                </div>
                            @elseif($wType === 'fixed_room')
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    <span class="badge bg-primary-subtle text-primary border">🏠 Khoán phòng</span>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                    Cố định 1 phòng
                                </div>
                            @else
                                <div class="small text-muted font-monospace" style="font-size: 0.75rem;">
                                    {{ $inv->water_old }} → {{ $inv->water_new }}
                                </div>
                                <div class="small text-dark fw-semibold" style="font-size: 0.75rem;">
                                    Dùng: <b>{{ $inv->water_usage }} m³</b> × {{ number_format($inv->water_rate, 0, ',', '.') }}đ
                                </div>
                            @endif
                        </td>

                        <!-- 6. Chi tiết các khoản phí dịch vụ (Wifi, Rác, Xe, Thang máy...) -->
                        <td>
                            @if(!empty($inv->fees_detail))
                                <div class="d-flex flex-column gap-1">
                                    @foreach($inv->fees_detail as $f)
                                        <div class="d-flex justify-content-between align-items-center bg-light px-2 py-1 rounded border small" style="font-size: 0.78rem;">
                                            <span>
                                                @if(str_contains(strtolower($f['name']), 'wifi') || str_contains(strtolower($f['name']), 'mạng'))
                                                    🌐
                                                @elseif(str_contains(strtolower($f['name']), 'rác') || str_contains(strtolower($f['name']), 'vệ sinh'))
                                                    🗑️
                                                @elseif(str_contains(strtolower($f['name']), 'xe'))
                                                    🛵
                                                @elseif(str_contains(strtolower($f['name']), 'thang máy'))
                                                    🛗
                                                @else
                                                    🏷️
                                                @endif
                                                {{ $f['name'] }}:
                                                @if(!empty($f['calc_desc']))
                                                    <span class="text-muted" style="font-size: 0.7rem;">({{ $f['calc_desc'] }})</span>
                                                @endif
                                            </span>
                                            <b class="text-dark font-monospace">{{ number_format($f['amount'], 0, ',', '.') }}đ</b>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between mt-1 pt-1 border-top small fw-bold">
                                    <span class="text-muted">Tổng phí DV:</span>
                                    <span class="text-primary">{{ number_format($inv->other_fees, 0, ',', '.') }}đ</span>
                                </div>
                            @else
                                <div class="text-muted small fst-italic">
                                    Tổng: {{ number_format($inv->other_fees, 0, ',', '.') }}đ
                                </div>
                            @endif
                        </td>

                        <!-- 7. Tổng tiền phải thu -->
                        <td class="text-end">
                            <div class="fw-bold text-danger fs-5">
                                {{ number_format($inv->total_amount, 0, ',', '.') }}đ
                            </div>
                            @if($inv->discount > 0)
                                <div class="small text-success">
                                    - Giảm: {{ number_format($inv->discount, 0, ',', '.') }}đ
                                </div>
                            @endif
                        </td>

                        <!-- 8. Đã thu / Còn nợ -->
                        <td class="text-end small">
                            <div class="text-success fw-bold">
                                Thu: {{ number_format($inv->paid_amount, 0, ',', '.') }}đ
                            </div>
                            <div class="{{ $inv->remaining_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                Nợ: {{ number_format($inv->remaining_amount, 0, ',', '.') }}đ
                            </div>
                        </td>

                        <!-- 9. Trạng thái -->
                        <td class="text-center">
                            <span class="badge bg-{{ $inv->status_badge }}-subtle text-{{ $inv->status_badge }} fw-bold px-2 py-1">
                                {{ $inv->status_label }}
                            </span>
                            <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                Hạn: {{ $inv->due_date->format('d/m/Y') }}
                            </div>
                        </td>

                        <!-- 10. Thao tác -->
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-primary py-1 px-2" title="Xem chi tiết, Quét VietQR & Ghi nhận thu tiền">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-dark py-1 px-2" title="In phiếu thu cho khách">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt text-muted fs-1 d-block mb-2"></i>
                            Chưa có hóa đơn nào cho các phòng trong <b>Tháng {{ $month }}/{{ $year }}</b>.
                            <div class="mt-3">
                                <a href="{{ route('invoices.bulk_create', ['property_id' => $propertyId, 'month' => $month, 'year' => $year]) }}" class="btn btn-warning text-dark fw-bold">
                                    <i class="bi bi-lightning-charge-fill me-1"></i> Bấm để chốt điện nước & lập bảng kê phí ngay
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
