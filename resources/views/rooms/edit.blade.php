@extends('layouts.app')

@section('title', 'Sửa Phòng ' . $room->room_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Chỉnh Sửa Phòng {{ $room->room_number }}</h5>
                @if($room->status !== 'occupied')
                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Xóa phòng</button>
                    </form>
                @endif
            </div>
            <div class="card-body p-4">
                <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Vị trí & Giá phòng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thuộc Nhà Trọ / Cơ Sở <span class="text-danger">*</span></label>
                            <select name="property_id" class="form-select" required>
                                @foreach($properties as $prop)
                                    <option value="{{ $prop->id }}" {{ old('property_id', $room->property_id) == $prop->id ? 'selected' : '' }}>
                                        🏢 {{ $prop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số / Mã phòng <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" class="form-control" required value="{{ old('room_number', $room->room_number) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tầng số <span class="text-danger">*</span></label>
                            <input type="number" name="floor" class="form-control" required min="1" max="50" value="{{ old('floor', $room->floor) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Giá thuê / tháng (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" required min="0" step="50000" value="{{ old('price', $room->price) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Diện tích (m²) <span class="text-danger">*</span></label>
                            <input type="number" name="area" class="form-control" required min="5" step="0.5" value="{{ old('area', $room->area) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sức chứa tối đa (người) <span class="text-danger">*</span></label>
                            <input type="number" name="max_tenants" class="form-control" required min="1" max="10" value="{{ old('max_tenants', $room->max_tenants) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Trạng thái phòng</label>
                            <select name="status" class="form-select">
                                <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>🟢 Phòng trống (Sẵn sàng cho thuê)</option>
                                <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>🔵 Đang cho thuê</option>
                                <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>🟡 Đang bảo trì / dọn dẹp</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Cấu hình Công tơ Điện & Nước</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mã công tơ điện riêng</label>
                            <input type="text" name="electricity_meter_number" class="form-control" value="{{ old('electricity_meter_number', $room->electricity_meter_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số điện khởi tạo (kWh) <span class="text-danger">*</span></label>
                            <input type="number" name="initial_electricity" class="form-control" required min="0" step="0.1" value="{{ old('initial_electricity', $room->initial_electricity) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá điện (VNĐ/kWh) <span class="text-danger">*</span></label>
                            <input type="number" name="electricity_rate" class="form-control" required min="0" step="100" value="{{ old('electricity_rate', $room->electricity_rate) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cách tính tiền nước <span class="text-danger">*</span></label>
                            <select name="water_calculation_type" class="form-select" required>
                                <option value="meter" {{ old('water_calculation_type', $room->water_calculation_type) == 'meter' ? 'selected' : '' }}>Theo đồng hồ con (m³)</option>
                                <option value="per_person" {{ old('water_calculation_type', $room->water_calculation_type) == 'per_person' ? 'selected' : '' }}>Theo đầu người ở</option>
                                <option value="fixed_room" {{ old('water_calculation_type', $room->water_calculation_type) == 'fixed_room' ? 'selected' : '' }}>Khoán cố định theo phòng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số nước khởi tạo (m³) <span class="text-danger">*</span></label>
                            <input type="number" name="initial_water" class="form-control" required min="0" step="0.1" value="{{ old('initial_water', $room->initial_water) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá nước (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="water_rate" class="form-control" required min="0" step="1000" value="{{ old('water_rate', $room->water_rate) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả thêm về phòng</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Cập Nhật Phòng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
