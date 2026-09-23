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
                <form action="{{ route('rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data">
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

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cách tính tiền mạng Wifi <span class="text-danger">*</span></label>
                            <select name="internet_type" class="form-select" required>
                                <option value="fixed" {{ old('internet_type', $room->internet_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Khoán cố định theo phòng (phòng/tháng)</option>
                                <option value="per_person" {{ old('internet_type', $room->internet_type) == 'per_person' ? 'selected' : '' }}>Theo đầu người ở (người/tháng)</option>
                                <option value="free" {{ old('internet_type', $room->internet_type) == 'free' ? 'selected' : '' }}>Miễn phí tiền mạng (0đ)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đơn giá tiền mạng Wifi (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="internet_rate" class="form-control" required min="0" step="1000" value="{{ old('internet_rate', $room->internet_rate ?? 100000) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả thêm về phòng</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Quản lý Hình ảnh chụp thực tế của phòng</h6>
                    
                    @if(!empty($room->images) && count($room->images) > 0)
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Các ảnh hiện có (Tích chọn ảnh muốn xóa):</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($room->images as $index => $imgPath)
                                    <div class="card p-1 shadow-sm border text-center position-relative" style="width: 140px;">
                                        <img src="{{ asset('storage/' . $imgPath) }}" class="rounded object-fit-cover w-100" style="height: 100px;" alt="Ảnh phòng {{ $room->room_number }}">
                                        <div class="form-check mt-1 d-flex align-items-center justify-content-center gap-1">
                                            <input class="form-check-input border-danger" type="checkbox" name="delete_images[]" value="{{ $imgPath }}" id="del_img_{{ $index }}">
                                            <label class="form-check-label small text-danger fw-bold" for="del_img_{{ $index }}">
                                                Xóa ảnh
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text text-danger mt-1"><i class="bi bi-info-circle"></i> Tích chọn vào ô "Xóa ảnh" và bấm Cập Nhật Phòng, hệ thống sẽ tự động xóa file vĩnh viễn khỏi máy chủ.</div>
                        </div>
                    @else
                        <div class="alert alert-light border small text-muted mb-3">
                            <i class="bi bi-camera me-1"></i> Phòng này hiện chưa có ảnh tải lên thực tế (đang dùng ảnh mẫu mặc định).
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tải thêm ảnh mới cho phòng:</label>
                        <input type="file" name="images[]" id="roomImagesEditInput" class="form-control" multiple accept="image/*">
                        <div class="form-text">Bạn có thể chọn thêm nhiều ảnh mới để bổ sung vào album ảnh phòng (JPG, PNG, WEBP, tối đa 5MB/ảnh).</div>
                        <div id="imageEditPreviewContainer" class="d-flex flex-wrap gap-2 mt-3"></div>
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

<script>
    document.getElementById('roomImagesEditInput').addEventListener('change', function(e) {
        const container = document.getElementById('imageEditPreviewContainer');
        container.innerHTML = '';
        const files = Array.from(e.target.files);
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const div = document.createElement('div');
                    div.className = 'position-relative border rounded p-1 bg-light shadow-sm';
                    div.style.width = '110px';
                    div.style.height = '110px';
                    div.innerHTML = `
                        <img src="${event.target.result}" class="w-100 h-100 rounded object-fit-cover" alt="Preview mới">
                        <span class="badge bg-success position-absolute bottom-0 start-50 translate-middle-x mb-1 small" style="font-size:0.65rem;">Mới ${index + 1}</span>
                    `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection
