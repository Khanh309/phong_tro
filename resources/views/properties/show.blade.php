@extends('layouts.app')

@section('title', $property->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0">{{ $property->name }}</h3>
            <span class="badge bg-primary-subtle text-primary">{{ $property->total_floors }} Tầng</span>
        </div>
        <p class="text-muted mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $property->address }}, {{ $property->district }}, {{ $property->city }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('rooms.create', ['property_id' => $property->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Thêm phòng vào nhà này
        </a>
        @if(auth()->user()?->isAdmin())
        <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
        </a>
        @endif
    </div>
</div>

<!-- THÔNG TIN TỔNG QUAN -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card p-3 h-100">
            <div class="fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Thông số cơ sở</div>
            <div class="small">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Tổng số phòng:</span>
                    <span class="fw-bold">{{ $property->rooms->count() }} phòng</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Đang cho thuê:</span>
                    <span class="fw-bold text-primary">{{ $property->rooms->where('status', 'occupied')->count() }} phòng</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Phòng trống:</span>
                    <span class="fw-bold text-success">{{ $property->rooms->where('status', 'available')->count() }} phòng</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Đang sửa / bảo trì:</span>
                    <span class="fw-bold text-warning">{{ $property->rooms->where('status', 'maintenance')->count() }} phòng</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card p-3 h-100">
            <div class="fw-bold text-dark mb-2"><i class="bi bi-bank text-success me-2"></i>Tài khoản nhận tiền VietQR</div>
            <div class="small">
                <div class="py-1 border-bottom">Ngân hàng: <b>{{ $property->bank_name ?: 'Chưa cấu hình' }}</b></div>
                <div class="py-1 border-bottom">Số tài khoản: <code class="fs-6 fw-bold">{{ $property->bank_account_number ?: '---' }}</code></div>
                <div class="py-1">Chủ tài khoản: <b>{{ $property->bank_account_holder ?: '---' }}</b></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card p-3 h-100">
            <div class="fw-bold text-dark mb-2"><i class="bi bi-lightning-charge text-warning me-2"></i>Hợp đồng Nhà nước</div>
            <div class="small">
                <div class="py-1 border-bottom">Mã khách hàng EVN: <code>{{ $property->electricity_meter_code ?: 'Chưa nhập' }}</code></div>
                <div class="py-1 border-bottom">Mã danh bộ Nước: <code>{{ $property->water_meter_code ?: 'Chưa nhập' }}</code></div>
                <div class="py-1 text-muted">{{ $property->description ?: 'Không có ghi chú thêm.' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- DANH SÁCH PHÒNG TRỌ CỦA CƠ SỞ -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark"><i class="bi bi-door-open-fill text-primary me-2"></i>Danh Sách Phòng Trọ ({{ $property->rooms->count() }} phòng)</span>
        <a href="{{ route('rooms.index', ['property_id' => $property->id]) }}" class="btn btn-sm btn-outline-primary">
            Xem dạng sơ đồ tầng
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Số Phòng</th>
                    <th>Tầng</th>
                    <th>Giá Thuê</th>
                    <th>Diện tích</th>
                    <th>Trạng thái</th>
                    <th>Khách đang thuê</th>
                    <th>Công tơ Điện</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($property->rooms as $room)
                    <tr>
                        <td>
                            <a href="{{ route('rooms.show', $room->id) }}" class="fw-bold text-primary text-decoration-none">
                                Phòng {{ $room->room_number }}
                            </a>
                        </td>
                        <td>Tầng {{ $room->floor }}</td>
                        <td class="fw-bold">{{ number_format($room->price, 0, ',', '.') }}đ</td>
                        <td>{{ $room->area }} m²</td>
                        <td>
                            <span class="badge bg-{{ $room->status_badge }}-subtle text-{{ $room->status_badge }}">
                                {{ $room->status_label }}
                            </span>
                        </td>
                        <td>
                            @if($room->currentContract && $room->currentContract->tenant)
                                <div>
                                    <a href="{{ route('tenants.show', $room->currentContract->tenant->id) }}" class="text-dark fw-bold text-decoration-none">
                                        👑 {{ $room->currentContract->tenant->name }}
                                    </a>
                                </div>
                                @if($room->currentContract->members && $room->currentContract->members->count() > 0)
                                    <div class="small text-muted mt-1">
                                        👥 <b>+{{ $room->currentContract->members->count() }} người ở cùng:</b>
                                        <div class="text-secondary">{{ $room->currentContract->members->pluck('name')->join(', ') }}</div>
                                    </div>
                                @endif
                                <div class="text-muted small mt-1"><i class="bi bi-telephone"></i> {{ $room->currentContract->tenant->phone }}</div>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">{{ $room->electricity_meter_number ?: '---' }}</span>
                            <div><small class="text-dark fw-semibold">{{ number_format($room->getLatestElectricityReading(), 1) }} kWh</small></div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-sm btn-outline-primary" title="Xem phòng">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-outline-secondary" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có phòng nào trong nhà trọ này.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
