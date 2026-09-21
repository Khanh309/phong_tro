@extends('layouts.app')

@section('title', 'Quản lý Chuỗi Nhà Trọ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">{{ auth()->user()?->isAdmin() ? 'Chuỗi Cơ Sở / Nhà Trọ' : 'Cơ Sở Nhà Trọ Của Bạn' }}</h3>
        <p class="text-muted mb-0">Quản lý nhiều tòa nhà, căn hộ trọ ở các địa chỉ khác nhau</p>
    </div>
    @if(auth()->user()?->isAdmin())
    <a href="{{ route('properties.create') }}" class="btn btn-primary fw-bold shadow-sm">
        <i class="bi bi-building-add me-1"></i> + Thêm Nhà Trọ Mới
    </a>
    @else
    <div class="badge bg-secondary-subtle text-secondary border px-3 py-2">
        <i class="bi bi-shield-lock me-1"></i> Chỉ Chủ trọ (Admin) mới có quyền tạo thêm cơ sở nhà trọ mới
    </div>
    @endif
</div>

<div class="row g-4">
    @forelse($properties as $property)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-2">Tòa nhà {{ $property->total_floors }} tầng</span>
                            <h5 class="card-title fw-bold text-dark mb-1">{{ $property->name }}</h5>
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $property->address }}, {{ $property->district }}, {{ $property->city }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <div class="row text-center g-2">
                            <div class="col-4 border-end">
                                <div class="text-muted small">Tổng phòng</div>
                                <div class="fw-bold fs-5 text-dark">{{ $property->rooms_count }}</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="text-muted small">Đang thuê</div>
                                <div class="fw-bold fs-5 text-primary">{{ $property->occupied_rooms_count }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Phòng trống</div>
                                <div class="fw-bold fs-5 text-success">{{ $property->available_rooms_count }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="small text-muted mb-3">
                        @if($property->bank_name && $property->bank_account_number)
                            <div class="mb-1"><i class="bi bi-bank text-primary me-1"></i> <b>{{ $property->bank_name }}</b>: {{ $property->bank_account_number }} ({{ $property->bank_account_holder }})</div>
                        @endif
                        @if($property->electricity_meter_code)
                            <div><i class="bi bi-lightning-charge text-warning me-1"></i> Mã EVN: <code>{{ $property->electricity_meter_code }}</code></div>
                        @endif
                    </div>

                    <div class="d-flex gap-2 pt-2 border-top">
                        <a href="{{ route('properties.show', $property->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="bi bi-eye"></i> Xem chi tiết
                        </a>
                        <a href="{{ route('rooms.index', ['property_id' => $property->id]) }}" class="btn btn-outline-secondary btn-sm" title="Xem sơ đồ phòng">
                            <i class="bi bi-grid-3x3-gap"></i> Sơ đồ phòng
                        </a>
                        @if(auth()->user()?->isAdmin())
                        <a href="{{ route('properties.edit', $property->id) }}" class="btn btn-outline-dark btn-sm" title="Chỉnh sửa nhà trọ">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-buildings text-muted fs-1 d-block mb-3"></i>
            <h5>Chưa có nhà trọ nào</h5>
            <p class="text-muted">Không tìm thấy cơ sở nhà trọ được phân công.</p>
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('properties.create') }}" class="btn btn-primary">Thêm nhà trọ ngay</a>
            @endif
        </div>
    @endforelse
</div>
@endsection
