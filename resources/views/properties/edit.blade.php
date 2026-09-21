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
                <form action="{{ route('properties.update', $property->id) }}" method="POST">
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

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Cập Nhật Thông Tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
