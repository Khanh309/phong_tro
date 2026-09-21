@extends('layouts.app')

@section('title', 'Chốt Điện Nước Nhanh Cả Nhà Trọ')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Chốt Số Điện Nước Cả Nhà Trọ (1 Phút)</h3>
        <p class="text-muted mb-0">Nhập nhanh chỉ số mới theo danh sách phòng, hệ thống tự tính tiền và xuất toàn bộ hóa đơn</p>
    </div>
</div>

<!-- CHỌN CƠ SỞ & KỲ HÓA ĐƠN -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('invoices.bulk_create') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <label class="form-label small fw-semibold text-muted mb-1">Chọn Cơ Sở / Nhà Trọ Chốt Sổ:</label>
            <select name="property_id" class="form-select" onchange="this.form.submit()">
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>
                        🏢 {{ $prop->name }} ({{ $prop->address }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Tháng tính tiền:</label>
            <select name="month" class="form-select" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Năm:</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-arrow-repeat me-1"></i> Tải lại danh sách</button>
        </div>
    </form>
</div>

<!-- FORM NHẬP CHỈ SỐ HÀNG LOẠT -->
@if($rooms->isEmpty())
    <div class="card p-5 text-center shadow-sm">
        <i class="bi bi-door-closed text-muted fs-1 mb-2"></i>
        <h5>Không có phòng nào đang thuê tại cơ sở này</h5>
        <p class="text-muted">Cơ sở này hiện tại toàn bộ phòng đang trống hoặc chưa có hợp đồng thuê.</p>
    </div>
@else
    <form action="{{ route('invoices.bulk_store') }}" method="POST" id="bulkForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $propertyId }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-dark fs-6">
                    Bảng Chốt Số: <span class="text-primary">{{ $selectedProperty->name }}</span> ({{ $rooms->count() }} phòng đang thuê)
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="small fw-semibold text-muted text-nowrap">Hạn nộp tiền chung:</label>
                    <input type="date" name="due_date" class="form-control form-control-sm" required value="{{ now()->copy()->startOfMonth()->addDays(5)->toDateString() }}">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 140px;">Phòng & Khách</th>
                            <th>Tiền Phòng</th>
                            <th style="width: 110px;">Điện Cũ</th>
                            <th style="width: 130px;">Điện Mới (kWh)</th>
                            <th style="width: 100px;">Đơn giá điện</th>
                            <th style="min-width: 200px;">💧 Tiền Nước (Đồng hồ / Người / Phòng)</th>
                            <th style="min-width: 180px;">🌐 Mạng Wifi & Phí Dịch Vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                            @php
                                $tenants = $room->total_occupants_count ?: 1;
                                $netFee = $room->calculateInternetAmount($tenants);
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary fs-6">Phòng {{ $room->room_number }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 130px;">
                                        {{ $room->currentContract->tenant->name ?? '---' }}
                                    </div>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                        👥 {{ $tenants }} người ở
                                    </span>
                                </td>
                                <td>
                                    <input type="number" name="rooms[{{ $room->id }}][room_price]" class="form-control form-control-sm" value="{{ $room->currentContract->rental_price ?? $room->price }}">
                                </td>
                                <td>
                                    <input type="number" step="0.1" name="rooms[{{ $room->id }}][electricity_old]" class="form-control form-control-sm bg-light" readonly value="{{ $room->old_elec }}">
                                </td>
                                <td>
                                    <input type="number" step="0.1" min="{{ $room->old_elec }}" name="rooms[{{ $room->id }}][electricity_new]" class="form-control form-control-sm fw-bold border-primary" placeholder="Số mới..." value="{{ $room->old_elec > 0 ? ($room->old_elec + rand(70, 150)) : 100 }}">
                                </td>
                                <td>
                                    <input type="number" name="rooms[{{ $room->id }}][electricity_rate]" class="form-control form-control-sm" value="{{ $room->electricity_rate }}">
                                </td>
                                <td>
                                    @if($room->water_calculation_type === 'per_person')
                                        <div class="p-2 rounded bg-info-subtle border border-info-subtle">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-info text-dark">👥 Tính theo người</span>
                                                <span class="fw-bold text-info-emphasis">{{ number_format($tenants * $room->water_rate, 0, ',', '.') }}đ</span>
                                            </div>
                                            <small class="text-dark d-block">
                                                <b>{{ $tenants }} người</b> × {{ number_format($room->water_rate, 0, ',', '.') }}đ/người
                                            </small>
                                            <input type="hidden" name="rooms[{{ $room->id }}][water_rate]" value="{{ $room->water_rate }}">
                                        </div>
                                    @elseif($room->water_calculation_type === 'fixed_room')
                                        <div class="p-2 rounded bg-primary-subtle border border-primary-subtle">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-primary">🏠 Khoán theo phòng</span>
                                                <span class="fw-bold text-primary">{{ number_format($room->water_rate, 0, ',', '.') }}đ</span>
                                            </div>
                                            <small class="text-muted d-block">Cố định 1 phòng</small>
                                            <input type="hidden" name="rooms[{{ $room->id }}][water_rate]" value="{{ $room->water_rate }}">
                                        </div>
                                    @else
                                        <!-- Theo đồng hồ con -->
                                        <div class="d-flex gap-1 align-items-center">
                                            <div style="width: 50%;">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">Số cũ</small>
                                                <input type="number" step="0.1" name="rooms[{{ $room->id }}][water_old]" class="form-control form-control-sm bg-light" readonly value="{{ $room->old_water }}">
                                            </div>
                                            <div style="width: 50%;">
                                                <small class="text-muted d-block" style="font-size: 0.72rem;">Số mới</small>
                                                <input type="number" step="0.1" min="{{ $room->old_water }}" name="rooms[{{ $room->id }}][water_new]" class="form-control form-control-sm fw-bold border-info" placeholder="Số mới..." value="{{ $room->old_water > 0 ? ($room->old_water + rand(4, 9)) : 5 }}">
                                            </div>
                                            <input type="hidden" name="rooms[{{ $room->id }}][water_rate]" value="{{ $room->water_rate }}">
                                        </div>
                                        <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Đồng hồ m³: {{ number_format($room->water_rate, 0, ',', '.') }}đ/m³</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        @if($room->internet_type === 'free')
                                            <span class="badge bg-success-subtle text-success mb-1">📶 Mạng: Miễn phí</span>
                                        @elseif($room->internet_type === 'per_person')
                                            <span class="badge bg-primary-subtle text-primary mb-1">
                                                📶 Mạng: {{ number_format($netFee, 0, ',', '.') }}đ ({{ $tenants }} người)
                                            </span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary mb-1">
                                                📶 Mạng: {{ number_format($room->internet_rate ?? 100000, 0, ',', '.') }}đ/phòng
                                            </span>
                                        @endif

                                        @foreach($room->fees->where('fee_name', '!=', 'Tiền mạng Internet Wifi') as $f)
                                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                                • {{ $f->fee_name }}: {{ number_format($f->calculateTotal($tenants), 0, ',', '.') }}đ
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Các phòng bỏ trống số điện mới sẽ không được tạo hóa đơn.
                </div>
                <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold px-4" onclick="return confirm('Xác nhận lưu chỉ số và phát hành toàn bộ hóa đơn tháng?')">
                    <i class="bi bi-check2-circle me-1"></i> Lưu & Xuất Toàn Bộ Hóa Đơn ({{ $rooms->count() }} phòng)
                </button>
            </div>
        </div>
    </form>
@endif
@endsection
