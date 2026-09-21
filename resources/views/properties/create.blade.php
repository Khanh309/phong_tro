@extends('layouts.app')

@section('title', 'Thêm Nhà Trọ Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-building-add text-primary me-2"></i>Thêm Cơ Sở / Nhà Trọ Mới</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('properties.store') }}" method="POST">
                    @csrf

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Thông tin chung về nhà trọ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tên nhà trọ / Tòa nhà <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="vd: Tòa nhà Green House Cầu Giấy" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số tầng <span class="text-danger">*</span></label>
                            <input type="number" name="total_floors" class="form-control" required min="1" max="50" value="{{ old('total_floors', 5) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Địa chỉ chi tiết (Số nhà, Ngõ/Ngách, Đường) <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" required placeholder="vd: Số 18 Ngõ 86 Duy Tân, Dịch Vọng Hậu" value="{{ old('address') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quận / Huyện</label>
                            <input type="text" name="district" class="form-control" placeholder="vd: Cầu Giấy" value="{{ old('district') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" required value="{{ old('city', 'Hà Nội') }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Tài khoản Ngân hàng nhận tiền (để tự động sinh mã VietQR)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngân hàng</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="vd: MBBANK, TECHCOMBANK, VCB" value="{{ old('bank_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số tài khoản</label>
                            <input type="text" name="bank_account_number" class="form-control" placeholder="vd: 0987654321" value="{{ old('bank_account_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chủ tài khoản (Không dấu)</label>
                            <input type="text" name="bank_account_holder" class="form-control text-uppercase" placeholder="vd: NGUYEN VAN A" value="{{ old('bank_account_holder') }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Mã Khách Hàng Điện & Nước Tổng (EVN / Cấp nước)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã khách hàng Điện lực EVN</label>
                            <input type="text" name="electricity_meter_code" class="form-control" placeholder="vd: EVN-HN-CG-10293" value="{{ old('electricity_meter_code') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã danh bộ Nước sạch tổng</label>
                            <input type="text" name="water_meter_code" class="form-control" placeholder="vd: CNHN-CG-88219" value="{{ old('water_meter_code') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả / Ghi chú về nhà trọ</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Thông tin về tiện ích chung, thang máy, khóa cổng, giờ giấc...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-check-lg me-1"></i> Lưu Nhà Trọ Mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
