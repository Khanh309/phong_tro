@extends('layouts.app')

@section('title', 'Báo Hỏng / Sửa Chữa Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tools text-primary me-2"></i>Ghi Nhận Sự Cố / Báo Hỏng Thiết Bị</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('maintenance.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phòng bị sự cố <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select" required>
                                <option value="">-- Chọn phòng --</option>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}" {{ (old('room_id') ?? $selectedRoomId) == $r->id ? 'selected' : '' }}>
                                        Phòng {{ $r->room_number }} - {{ $r->property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày tiếp nhận báo <span class="text-danger">*</span></label>
                            <input type="date" name="reported_date" class="form-control" required value="{{ old('reported_date', now()->toDateString()) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tiêu đề sự cố / Hỏng hóc <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="vd: Điều hòa không mát, Vòi sen bị rỉ nước, Bóng đèn chớp..." value="{{ old('title') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả chi tiết hiện trạng</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Mô tả cụ thể khách báo gì, cần mua vật tư gì để thay...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Trạng thái xử lý <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>🔴 Chờ xử lý / Chưa sửa</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>🟡 Đang gọi thợ / Đang sửa</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>🟢 Đã sửa xong</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chi phí sửa chữa (VNĐ)</label>
                            <input type="number" name="cost" class="form-control" min="0" step="10000" placeholder="0" value="{{ old('cost', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày hoàn thành (nếu đã xong)</label>
                            <input type="date" name="resolved_date" class="form-control" value="{{ old('resolved_date') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Sự Cố</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
