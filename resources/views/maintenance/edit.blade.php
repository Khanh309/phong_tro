@extends('layouts.app')

@section('title', 'Cập Nhật Sự Cố')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Cập Nhật Tiến Độ Sửa Chữa</h5>
                <form action="{{ route('maintenance.destroy', $maintenance->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Xóa</button>
                </form>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('maintenance.update', $maintenance->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phòng bị sự cố <span class="text-danger">*</span></label>
                            <select name="room_id" class="form-select" required>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}" {{ old('room_id', $maintenance->room_id) == $r->id ? 'selected' : '' }}>
                                        Phòng {{ $r->room_number }} - {{ $r->property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày tiếp nhận báo <span class="text-danger">*</span></label>
                            <input type="date" name="reported_date" class="form-control" required value="{{ old('reported_date', $maintenance->reported_date->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Tiêu đề sự cố <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required value="{{ old('title', $maintenance->title) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $maintenance->description) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Trạng thái xử lý <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status', $maintenance->status) == 'pending' ? 'selected' : '' }}>🔴 Chờ xử lý</option>
                                <option value="in_progress" {{ old('status', $maintenance->status) == 'in_progress' ? 'selected' : '' }}>🟡 Đang sửa chữa</option>
                                <option value="completed" {{ old('status', $maintenance->status) == 'completed' ? 'selected' : '' }}>🟢 Đã hoàn thành</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chi phí sửa chữa (VNĐ)</label>
                            <input type="number" name="cost" class="form-control" min="0" step="10000" value="{{ old('cost', $maintenance->cost) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày hoàn thành</label>
                            <input type="date" name="resolved_date" class="form-control" value="{{ old('resolved_date', $maintenance->resolved_date ? $maintenance->resolved_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Cập Nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
