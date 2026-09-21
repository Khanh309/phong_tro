@extends('layouts.app')

@section('title', 'Sửa Khách Thuê - ' . $tenant->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Chỉnh Sửa Hồ Sơ Khách Thuê</h5>
                @if(!$tenant->currentContract)
                    <form action="{{ route('tenants.destroy', $tenant->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa khách này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Xóa khách</button>
                    </form>
                @endif
            </div>
            <div class="card-body p-4">
                <form action="{{ route('tenants.update', $tenant->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Thông tin cá nhân & Liên hệ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $tenant->name) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required value="{{ old('phone', $tenant->phone) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Giới tính <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Nam" {{ old('gender', $tenant->gender) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender', $tenant->gender) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender', $tenant->gender) == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $tenant->dob ? $tenant->dob->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Giấy tờ tùy thân & Pháp lý</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số CCCD / CMND</label>
                            <input type="text" name="id_card_number" class="form-control" value="{{ old('id_card_number', $tenant->id_card_number) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày cấp</label>
                            <input type="date" name="id_card_date" class="form-control" value="{{ old('id_card_date', $tenant->id_card_date ? $tenant->id_card_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nơi cấp</label>
                            <input type="text" name="id_card_place" class="form-control" value="{{ old('id_card_place', $tenant->id_card_place) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quê quán</label>
                            <input type="text" name="hometown" class="form-control" value="{{ old('hometown', $tenant->hometown) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Biển số xe máy / Ô tô</label>
                            <input type="text" name="vehicle_plate" class="form-control" value="{{ old('vehicle_plate', $tenant->vehicle_plate) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tình trạng khai báo tạm trú <span class="text-danger">*</span></label>
                            <select name="temporary_residence_status" class="form-select" required>
                                <option value="not_registered" {{ old('temporary_residence_status', $tenant->temporary_residence_status) == 'not_registered' ? 'selected' : '' }}>Chưa đăng ký tạm trú</option>
                                <option value="registered" {{ old('temporary_residence_status', $tenant->temporary_residence_status) == 'registered' ? 'selected' : '' }}>Đã khai báo với Công an</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $tenant->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tenants.show', $tenant->id) }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Cập Nhật Hồ Sơ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
