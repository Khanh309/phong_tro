@extends('layouts.app')

@section('title', 'Sửa Nhà Trọ - ' . $property->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Chỉnh Sửa Nhà Trọ</h5>
                <form action="{{ route('properties.destroy', $property->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà trọ này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </form>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Thông tin chung về nhà trọ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tên nhà trọ / Tòa nhà <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $property->name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số tầng <span class="text-danger">*</span></label>
                            <input type="number" name="total_floors" class="form-control" required min="1" max="50" value="{{ old('total_floors', $property->total_floors) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" required value="{{ old('address', $property->address) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quận / Huyện</label>
                            <input type="text" name="district" class="form-control" value="{{ old('district', $property->district) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" required value="{{ old('city', $property->city) }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Tài khoản Ngân hàng nhận tiền (VietQR)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngân hàng</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $property->bank_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số tài khoản</label>
                            <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $property->bank_account_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chủ tài khoản</label>
                            <input type="text" name="bank_account_holder" class="form-control text-uppercase" value="{{ old('bank_account_holder', $property->bank_account_holder) }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Mã Khách Hàng Điện & Nước Tổng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã khách hàng Điện lực EVN</label>
                            <input type="text" name="electricity_meter_code" class="form-control" value="{{ old('electricity_meter_code', $property->electricity_meter_code) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã danh bộ Nước sạch tổng</label>
                            <input type="text" name="water_meter_code" class="form-control" value="{{ old('water_meter_code', $property->water_meter_code) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả / Ghi chú</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $property->description) }}</textarea>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">4. Cấu hình Tiền Nước & Tiền Mạng Mặc Định Của Nhà Trọ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cách tính tiền nước mặc định <span class="text-danger">*</span></label>
                            <select name="default_water_type" class="form-select" required>
                                <option value="meter" {{ old('default_water_type', $property->default_water_type ?? 'meter') == 'meter' ? 'selected' : '' }}>Theo đồng hồ con (m³)</option>
                                <option value="per_person" {{ old('default_water_type', $property->default_water_type) == 'per_person' ? 'selected' : '' }}>Theo đầu người (người/tháng)</option>
                                <option value="fixed_room" {{ old('default_water_type', $property->default_water_type) == 'fixed_room' ? 'selected' : '' }}>Khoán cố định theo phòng (phòng/tháng)</option>
                            </select>
                            <small class="text-muted">Tùy chọn: Đồng hồ khối, theo đầu người, hoặc khoán phòng</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đơn giá nước mặc định (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="default_water_rate" class="form-control" required min="0" step="1000" value="{{ old('default_water_rate', $property->default_water_rate ?? 30000) }}">
                            <small class="text-muted">Đơn giá theo m³, theo người, hoặc khoán 1 phòng</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cách tính tiền mạng Internet Wifi mặc định <span class="text-danger">*</span></label>
                            <select name="default_internet_type" class="form-select" required>
                                <option value="fixed" {{ old('default_internet_type', $property->default_internet_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Khoán cố định theo phòng (phòng/tháng)</option>
                                <option value="per_person" {{ old('default_internet_type', $property->default_internet_type) == 'per_person' ? 'selected' : '' }}>Theo đầu người (người/tháng)</option>
                                <option value="free" {{ old('default_internet_type', $property->default_internet_type) == 'free' ? 'selected' : '' }}>Miễn phí tiền mạng (0đ)</option>
                            </select>
                            <small class="text-muted">Tùy chọn: Khoán theo phòng, theo đầu người, hoặc miễn phí</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đơn giá tiền mạng mặc định (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="default_internet_rate" class="form-control" required min="0" step="1000" value="{{ old('default_internet_rate', $property->default_internet_rate ?? 100000) }}">
                            <small class="text-muted">Đơn giá mạng theo phòng hoặc theo từng người</small>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">5. Ảnh đại diện cơ sở / tòa nhà</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            @if($property->image)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Ảnh hiện tại:</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $property->image) }}" class="rounded border shadow-sm" style="max-height: 140px; object-fit: cover;" alt="{{ $property->name }}">
                                        <div class="form-check">
                                            <input class="form-check-input border-danger" type="checkbox" name="remove_image" value="1" id="removePropertyImage">
                                            <label class="form-check-label text-danger fw-bold small" for="removePropertyImage">
                                                Xóa ảnh này (Sử dụng ảnh mẫu mặc định)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <label class="form-label fw-semibold">Thay đổi ảnh đại diện tòa nhà:</label>
                            <input type="file" name="image" id="propertyImageEditInput" class="form-control" accept="image/*">
                            <div class="form-text">Chọn ảnh mới để thay thế ảnh hiện tại (JPG, PNG, WEBP, tối đa 5MB).</div>
                            <div id="propertyImageEditPreview" class="mt-2" style="display: none;">
                                <img src="" id="propertyImageEditPreviewImg" class="rounded border shadow-sm" style="max-height: 140px; object-fit: cover;" alt="Xem trước mới">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Cập Nhật Thông Tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('propertyImageEditInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('propertyImageEditPreviewImg').src = event.target.result;
                document.getElementById('propertyImageEditPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
