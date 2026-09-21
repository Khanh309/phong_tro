@extends('layouts.app')

@section('title', 'Thêm Khách Thuê Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i>Thêm Khách Thuê Trọ Mới</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('tenants.store') }}" method="POST">
                    @csrf

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Thông tin cá nhân & Liên hệ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="vd: Nguyễn Văn A" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required placeholder="vd: 0912345678" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Giới tính <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Nam" {{ old('gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="vd: nguyenvana@gmail.com" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Giấy tờ tùy thân (CCCD) & Pháp lý</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số CCCD / CMND</label>
                            <input type="text" name="id_card_number" class="form-control" placeholder="vd: 001098012345" value="{{ old('id_card_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày cấp</label>
                            <input type="date" name="id_card_date" class="form-control" value="{{ old('id_card_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nơi cấp</label>
                            <input type="text" name="id_card_place" class="form-control" placeholder="vd: Cục Cảnh sát QLHC về TTXH" value="{{ old('id_card_place') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quê quán / Thường trú</label>
                            <input type="text" name="hometown" class="form-control" placeholder="vd: Nam Định, Thái Bình..." value="{{ old('hometown') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Biển số xe máy / Ô tô</label>
                            <input type="text" name="vehicle_plate" class="form-control" placeholder="vd: 18B2-678.90" value="{{ old('vehicle_plate') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tình trạng khai báo tạm trú <span class="text-danger">*</span></label>
                            <select name="temporary_residence_status" class="form-select" required>
                                <option value="not_registered" {{ old('temporary_residence_status') == 'not_registered' ? 'selected' : '' }}>Chưa đăng ký tạm trú</option>
                                <option value="registered" {{ old('temporary_residence_status') == 'registered' ? 'selected' : '' }}>Đã khai báo với Công an</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ghi chú về khách</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Nghề nghiệp, công ty làm việc, thói quen sinh hoạt...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Hồ Sơ Khách</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
