@extends('layouts.app')

@section('title', 'Sơ Đồ & Biểu Phí Phòng Trọ')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Sơ Đồ & Danh Sách Phòng Trọ</h3>
        <p class="text-muted mb-0">Xem trực quan tình trạng phòng theo từng tầng và cơ sở, kèm biểu phí dịch vụ từng phòng</p>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('properties.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-building-add me-1"></i> + Thêm Nhà Trọ Mới
            </a>
        @endif
        <a href="{{ route('rooms.vacant_finder') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-search me-1"></i> Tìm phòng trống
        </a>
        <a href="{{ route('rooms.create', ['property_id' => $propertyId]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Thêm Phòng Mới
        </a>
    </div>
</div>

<!-- THANH ĐIỀU HƯỚNG DẠNG TAB: SƠ ĐỒ PHÒNG vs BẢNG TỔNG HỢP BIỂU PHÍ -->
<ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded shadow-sm border" id="roomViewTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('view') !== 'fees' ? 'active' : '' }} fw-bold" id="grid-tab" data-bs-toggle="tab" data-bs-target="#tab-grid" type="button" role="tab">
            <i class="bi bi-grid-fill me-1"></i> Sơ Đồ Phòng Theo Tầng
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('view') === 'fees' ? 'active' : '' }} fw-bold" id="fees-tab" data-bs-toggle="tab" data-bs-target="#tab-fees" type="button" role="tab">
            <i class="bi bi-receipt-cutoff me-1"></i> Bảng Tổng Hợp Biểu Phí Tất Cả Các Phòng
            <span class="badge bg-warning text-dark ms-1">{{ $rooms->sum(fn($r) => $r->fees->count()) }} khoản phí</span>
        </button>
    </li>
</ul>

<!-- THANH BỘ LỌC CHUNG -->
<div class="card p-3 mb-4 shadow-sm border-0">
    <form method="GET" action="{{ route('rooms.index') }}" class="row g-2 align-items-center">
        <input type="hidden" name="view" id="currentViewInput" value="{{ request('view', 'grid') }}">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Cơ sở / Nhà trọ:</label>
            <select name="property_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">🏢 Tất cả nhà trọ</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ $propertyId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Trạng thái phòng:</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="available" {{ $status == 'available' ? 'selected' : '' }}>🟢 Phòng trống</option>
                <option value="occupied" {{ $status == 'occupied' ? 'selected' : '' }}>🔵 Đang cho thuê</option>
                <option value="maintenance" {{ $status == 'maintenance' ? 'selected' : '' }}>🟡 Đang bảo trì / dọn dẹp</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Tầng:</label>
            <select name="floor" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả tầng</option>
                @for($f = 1; $f <= 10; $f++)
                    <option value="{{ $f }}" {{ $floor == $f ? 'selected' : '' }}>Tầng {{ $f }}</option>
                @endfor
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end gap-2">
            <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">Đặt lại bộ lọc</a>
        </div>
    </form>
</div>

<div class="tab-content" id="roomViewTabContent">
    <!-- TAB 1: SƠ ĐỒ PHÒNG THEO TẦNG -->
    <div class="tab-pane fade {{ request('view') !== 'fees' ? 'show active' : '' }}" id="tab-grid" role="tabpanel">
        @if($roomsByFloor->isEmpty())
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-door-closed text-muted fs-1 d-block mb-3"></i>
                <h5>Không tìm thấy phòng nào phù hợp</h5>
                <p class="text-muted">Thử thay đổi bộ lọc hoặc thêm phòng mới.</p>
            </div>
        @else
            @foreach($roomsByFloor as $floorNumber => $floorRooms)
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <span class="badge bg-dark fs-6 px-3 py-1">TẦNG {{ $floorNumber }}</span>
                        <span class="text-muted small">({{ $floorRooms->count() }} phòng)</span>
                    </div>

                    <div class="row g-3">
                        @foreach($floorRooms as $room)
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <div class="card h-100 shadow-sm border-2 {{ $room->status === 'available' ? 'border-success' : ($room->status === 'occupied' ? 'border-primary' : 'border-warning') }}">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="fw-bold mb-0">
                                                    <a href="{{ route('rooms.show', $room->id) }}" class="text-dark text-decoration-none">
                                                        Phòng {{ $room->room_number }}
                                                    </a>
                                                </h5>
                                                <div class="text-muted small" style="font-size: 0.78rem;">{{ $room->property->name }}</div>
                                            </div>
                                            <span class="badge bg-{{ $room->status_badge }}-subtle text-{{ $room->status_badge }} fw-bold">
                                                {{ $room->status_label }}
                                            </span>
                                        </div>

                                        <div class="fw-bold text-primary fs-5 mb-2">
                                            {{ number_format($room->price, 0, ',', '.') }}đ <small class="text-muted fs-6 fw-normal">/tháng</small>
                                        </div>

                                        <!-- THÔNG SỐ ĐIỆN NƯỚC CỦA PHÒNG -->
                                        <div class="bg-light p-2 rounded small mb-2 border">
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>Diện tích: <b>{{ $room->area }} m²</b></span>
                                                <span>Tối đa: <b>{{ $room->max_tenants }} người</b></span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted mt-1">
                                                <span>⚡ Điện: <b class="text-dark">{{ number_format($room->electricity_rate, 0, ',', '.') }}đ/kWh</b></span>
                                                <span>💧 Nước: <b class="text-dark">{{ number_format($room->water_rate, 0, ',', '.') }}đ</b></span>
                                            </div>
                                        </div>

                                        <!-- CÁC KHOẢN PHÍ DỊCH VỤ ĐI KÈM PHÒNG -->
                                        <div class="p-2 rounded mb-2 border bg-white flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark" style="font-size: 0.75rem;">
                                                    <i class="bi bi-tags-fill text-warning me-1"></i>Khoản phí dịch vụ ({{ $room->fees->count() }}):
                                                </span>
                                                <a href="{{ route('rooms.show', $room->id) }}#addFeeModal" class="text-primary text-decoration-none small" style="font-size: 0.7rem;">+ Thêm phí</a>
                                            </div>
                                            @if($room->fees->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($room->fees as $fee)
                                                        <span class="badge bg-secondary-subtle text-secondary border small py-1 px-2 text-truncate" style="max-width: 100%;" title="{{ $fee->fee_name }}: {{ number_format($fee->unit_price) }}đ ({{ $fee->fee_type }})">
                                                            {{ $fee->fee_name }}: <b class="text-dark">{{ number_format($fee->unit_price, 0, ',', '.') }}đ</b>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted fst-italic small text-center py-1">Chưa cấu hình phí riêng</div>
                                            @endif
                                        </div>

                                        <!-- KHÁCH ĐANG THUÊ (TẤT CẢ NGƯỜI Ở CÙNG) -->
                                        @if($room->status === 'occupied' && $room->currentContract && $room->currentContract->tenant)
                                            @php
                                                $mainTenant = $room->currentContract->tenant;
                                                $members = $room->currentContract->members;
                                                $totalOccupants = 1 + $members->count();
                                            @endphp
                                            <div class="small border rounded p-2 mb-2 bg-light">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-muted fw-bold" style="font-size: 0.75rem;">
                                                        <i class="bi bi-people-fill text-primary me-1"></i>Khách đang ở ({{ $totalOccupants }} người):
                                                    </span>
                                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.7rem;">{{ $totalOccupants }}/{{ $room->max_tenants }}</span>
                                                </div>
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;">
                                                    <i class="bi bi-person-fill-check text-success me-1"></i>{{ $mainTenant->name }} <small class="text-muted fw-normal">(Đại diện)</small>
                                                </div>
                                                @if($members->isNotEmpty())
                                                    <div class="mt-1 pt-1 border-top" style="font-size: 0.72rem;">
                                                        @foreach($members as $m)
                                                            <div class="text-muted text-truncate">• {{ $m->name }} <span class="badge bg-white text-secondary border">({{ $m->relationship ?: 'ở cùng' }})</span></div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($room->status === 'available')
                                            <div class="small p-2 text-center text-success bg-success-subtle rounded mb-2 fw-semibold">
                                                <i class="bi bi-check-circle"></i> Sẵn sàng cho thuê ngay
                                            </div>
                                        @else
                                            <div class="small p-2 text-center text-warning bg-warning-subtle rounded mb-2 fw-semibold">
                                                <i class="bi bi-tools"></i> Đang dọn phòng / sửa chữa
                                            </div>
                                        @endif

                                        <!-- NÚT TÁC VỤ -->
                                        <div class="d-flex gap-1 pt-2 border-top mt-auto">
                                            <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                                Chi tiết & Phí
                                            </a>
                                            @if($room->status === 'available')
                                                <a href="{{ route('contracts.create', ['room_id' => $room->id]) }}" class="btn btn-success btn-sm" title="Lập hợp đồng cho thuê">
                                                    <i class="bi bi-person-plus-fill"></i> Cho thuê
                                                </a>
                                            @elseif($room->status === 'occupied')
                                                <a href="{{ route('invoices.create', ['room_id' => $room->id]) }}" class="btn btn-outline-warning text-dark btn-sm" title="Lập hóa đơn phòng">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- TAB 2: BẢNG TỔNG HỢP BIỂU PHÍ TẤT CẢ CÁC PHÒNG -->
    <div class="tab-pane fade {{ request('view') === 'fees' ? 'show active' : '' }}" id="tab-fees" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark fs-5">
                    <i class="bi bi-table text-primary me-2"></i>Bảng Kê Biểu Phí & Dịch Vụ Của Toàn Bộ Các Phòng
                </span>
                <span class="badge bg-primary fs-6">{{ $rooms->count() }} phòng</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 130px;">Phòng</th>
                            <th style="width: 140px;">Cơ Sở</th>
                            <th style="width: 130px;">Trạng Thái</th>
                            <th class="text-end" style="width: 130px;">Tiền Thuê/Tháng</th>
                            <th class="text-end" style="width: 120px;">⚡ Giá Điện</th>
                            <th class="text-end" style="width: 120px;">💧 Giá Nước</th>
                            <th>Các Khoản Phí Dịch Vụ Cố Định Đi Kèm (Wifi, Rác, Gửi Xe, Thang Máy...)</th>
                            <th class="text-center" style="width: 110px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $r)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('rooms.show', $r->id) }}" class="text-decoration-none">
                                        Phòng {{ $r->room_number }}
                                    </a>
                                    <div class="small text-muted">Tầng {{ $r->floor }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $r->property->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $r->status_badge }}-subtle text-{{ $r->status_badge }}">
                                        {{ $r->status_label }}
                                    </span>
                                    @if($r->currentContract && $r->currentContract->tenant)
                                        @php
                                            $mainT = $r->currentContract->tenant;
                                            $memCount = $r->currentContract->members->count();
                                        @endphp
                                        <div class="small text-dark mt-1">
                                            <b>👤 {{ $mainT->name }}</b>
                                            @if($memCount > 0)
                                                <div class="text-primary fw-semibold" style="font-size: 0.72rem;">
                                                    + {{ $memCount }} người ở cùng (Tổng: {{ 1 + $memCount }}/{{ $r->max_tenants }})
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    {{ number_format($r->price, 0, ',', '.') }}đ
                                </td>
                                <td class="text-end font-monospace">
                                    {{ number_format($r->electricity_rate, 0, ',', '.') }}đ<small class="text-muted">/kWh</small>
                                </td>
                                <td class="text-end font-monospace">
                                    {{ number_format($r->water_rate, 0, ',', '.') }}đ<small class="text-muted">/{{ $r->water_calculation_type === 'per_person' ? 'người' : 'm³' }}</small>
                                </td>
                                <td>
                                    @if($r->fees->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($r->fees as $f)
                                                <span class="badge bg-light text-dark border py-1 px-2 small">
                                                    <b>{{ $f->fee_name }}</b>: {{ number_format($f->unit_price, 0, ',', '.') }}đ
                                                    <span class="text-muted">({{ $f->fee_type === 'fixed' ? 'cố định' : ($f->fee_type === 'per_person' ? 'đầu người' : 'theo số lượng') }})</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Chưa cấu hình khoản phí nào</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('rooms.show', $r->id) }}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết & Quản lý phí">
                                        <i class="bi bi-gear-fill me-1"></i> Quản lý phí
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy phòng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const gridTab = document.getElementById('grid-tab');
        const feesTab = document.getElementById('fees-tab');
        const viewInput = document.getElementById('currentViewInput');

        if (gridTab && feesTab && viewInput) {
            gridTab.addEventListener('shown.bs.tab', function () {
                viewInput.value = 'grid';
            });
            feesTab.addEventListener('shown.bs.tab', function () {
                viewInput.value = 'fees';
            });
        }
    });
</script>
@endsection
