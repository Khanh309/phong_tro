@extends('layouts.app')

@section('title', 'Thêm Phòng Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-door-open-fill text-primary me-2"></i>Thêm Phòng Trọ Mới</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('rooms.store') }}" method="POST">
                    @csrf

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Vị trí & Giá phòng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thuộc Nhà Trọ / Cơ Sở <span class="text-danger">*</span></label>
                            <select name="property_id" class="form-select" required>
                                @foreach($properties as $prop)
                                    <option value="{{ $prop->id }}" {{ (old('property_id') ?? $selectedPropertyId) == $prop->id ? 'selected' : '' }}>
                                        🏢 {{ $prop->name }} ({{ $prop->address }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số / Mã phòng <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" class="form-control" required placeholder="vd: 101, 202" value="{{ old('room_number') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tầng số <span class="text-danger">*</span></label>
                            <input type="number" name="floor" class="form-control" required min="1" max="50" value="{{ old('floor', 1) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Giá thuê / tháng (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" required min="0" step="50000" placeholder="vd: 3500000" value="{{ old('price', 3000000) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Diện tích (m²) <span class="text-danger">*</span></label>
                            <input type="number" name="area" class="form-control" required min="5" step="0.5" value="{{ old('area', 22.0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sức chứa tối đa (người) <span class="text-danger">*</span></label>
                            <input type="number" name="max_tenants" class="form-control" required min="1" max="10" value="{{ old('max_tenants', 2) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Trạng thái phòng ban đầu</label>
                            <select name="status" class="form-select">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>🟢 Phòng trống (Sẵn sàng cho thuê)</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>🔵 Đang cho thuê</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>🟡 Đang bảo trì / dọn dẹp</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Cấu hình Công tơ Điện & Nước riêng của phòng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã / Số công tơ điện riêng</label>
                            <input type="text" name="electricity_meter_number" class="form-control" placeholder="vd: EM-101" value="{{ old('electricity_meter_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số điện ban đầu (kWh) <span class="text-danger">*</span></label>
                            <input type="number" name="initial_electricity" class="form-control" required min="0" step="0.1" value="{{ old('initial_electricity', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá điện (VNĐ/kWh) <span class="text-danger">*</span></label>
                            <input type="number" name="electricity_rate" class="form-control" required min="0" step="100" value="{{ old('electricity_rate', 3800) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cách tính tiền nước <span class="text-danger">*</span></label>
                            <select name="water_calculation_type" class="form-select" required>
                                <option value="meter" {{ old('water_calculation_type') == 'meter' ? 'selected' : '' }}>Theo đồng hồ con (m³)</option>
                                <option value="per_person" {{ old('water_calculation_type') == 'per_person' ? 'selected' : '' }}>Theo đầu người ở (người/tháng)</option>
                                <option value="fixed_room" {{ old('water_calculation_type') == 'fixed_room' ? 'selected' : '' }}>Khoán cố định theo phòng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số nước ban đầu (m³) <span class="text-danger">*</span></label>
                            <input type="number" name="initial_water" class="form-control" required min="0" step="0.1" value="{{ old('initial_water', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá nước (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="water_rate" class="form-control" required min="0" step="1000" value="{{ old('water_rate', 30000) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả thêm về phòng (cửa sổ, ban công, nội thất)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Phòng có ban công thoáng mát, gác xép, kệ bếp...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Phòng Mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
