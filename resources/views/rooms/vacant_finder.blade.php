@extends('layouts.app')

@section('title', 'Tìm Kiếm Phòng Trống Nhanh')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-search text-success me-2"></i>Tra Cứu Phòng Trống Nhanh</h3>
        <p class="text-muted mb-0">Tìm nhanh phòng trống trên toàn bộ chuỗi cơ sở để tư vấn khách gọi hỏi thuê</p>
    </div>
    <span class="badge bg-success fs-6 px-3 py-2">Tìm thấy {{ $vacantRooms->count() }} phòng trống</span>
</div>

<!-- FORM TÌM KIẾM NHANH -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('rooms.vacant_finder') }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Cơ sở / Khu vực:</label>
            <select name="property_id" class="form-select">
                <option value="">🏢 Tất cả các nhà trọ</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>{{ $prop->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Giá tối thiểu (VNĐ):</label>
            <input type="number" name="min_price" class="form-control" placeholder="vd: 2500000" step="100000" value="{{ $minPrice }}">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Giá tối đa (VNĐ):</label>
            <input type="number" name="max_price" class="form-control" placeholder="vd: 4000000" step="100000" value="{{ $maxPrice }}">
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel-fill me-1"></i> Lọc phòng</button>
            <a href="{{ route('rooms.vacant_finder') }}" class="btn btn-outline-secondary" title="Đặt lại"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>
</div>

<!-- KẾT QUẢ DANH SÁCH PHÒNG TRỐNG -->
<div class="row g-4">
    @forelse($vacantRooms as $room)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border-2 border-success">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-success mb-2">🟢 Trống - Ở ngay</span>
                            <h4 class="fw-bold mb-0 text-dark">Phòng {{ $room->room_number }}</h4>
                            <div class="text-muted small"><i class="bi bi-building"></i> {{ $room->property->name }}</div>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small">Tầng {{ $room->floor }}</span>
                        </div>
                    </div>

                    <div class="my-3 p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <span class="text-muted">Giá thuê:</span>
                            <span class="fw-bold text-success fs-4">{{ number_format($room->price, 0, ',', '.') }}đ<small class="fs-6 text-muted">/th</small></span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Diện tích: <b>{{ $room->area }} m²</b></span>
                            <span>Ở tối đa: <b>{{ $room->max_tenants }} người</b></span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Điện: <b>{{ number_format($room->electricity_rate) }}đ/kWh</b></span>
                            <span>Nước: <b>{{ number_format($room->water_rate) }}đ</b></span>
                        </div>
                    </div>

                    <div class="small text-muted mb-3">
                        <div><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $room->property->address }}, {{ $room->property->district }}</div>
                        @if($room->description)
                            <div class="mt-1 text-dark fst-italic">"{{ $room->description }}"</div>
                        @endif
                    </div>

                    <div class="d-flex gap-2 pt-2 border-top">
                        <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="bi bi-eye"></i> Xem phòng
                        </a>
                        <a href="{{ route('contracts.create', ['room_id' => $room->id]) }}" class="btn btn-success btn-sm flex-grow-1 fw-bold">
                            <i class="bi bi-person-plus-fill"></i> Ký hợp đồng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-emoji-smile text-muted fs-1 d-block mb-3"></i>
            <h5>Hiện tại không có phòng trống nào theo tiêu chí lọc!</h5>
            <p class="text-muted">Toàn bộ phòng trong tầm giá/khu vực này đang được thuê hết.</p>
        </div>
    @endforelse
</div>
@endsection
