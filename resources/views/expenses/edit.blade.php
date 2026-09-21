@extends('layouts.app')

@section('title', 'Sửa Khoản Chi Phí')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-danger me-2"></i>Chỉnh Sửa Khoản Chi Phí</h5>
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa khoản chi này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Xóa</button>
                </form>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Áp dụng cho Nhà trọ / Cơ sở <span class="text-danger">*</span></label>
                            <select name="property_id" class="form-select" required>
                                @foreach($properties as $prop)
                                    <option value="{{ $prop->id }}" {{ old('property_id', $expense->property_id) == $prop->id ? 'selected' : '' }}>
                                        🏢 {{ $prop->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tháng chi <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('month', $expense->month) == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Năm <span class="text-danger">*</span></label>
                            <select name="year" class="form-select" required>
                                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ old('year', $expense->year) == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Loại chi phí đầu ra <span class="text-danger">*</span></label>
                            <select name="expense_type" class="form-select" required>
                                <option value="electricity_evn" {{ old('expense_type', $expense->expense_type) == 'electricity_evn' ? 'selected' : '' }}>⚡ Hóa đơn Điện tổng trả Điện lực EVN</option>
                                <option value="water_supply" {{ old('expense_type', $expense->expense_type) == 'water_supply' ? 'selected' : '' }}>💧 Hóa đơn Nước sinh hoạt tổng (Cấp nước)</option>
                                <option value="state_tax" {{ old('expense_type', $expense->expense_type) == 'state_tax' ? 'selected' : '' }}>🏛️ Thuế môn bài / Thuế kinh doanh nộp Nhà nước</option>
                                <option value="internet_bill" {{ old('expense_type', $expense->expense_type) == 'internet_bill' ? 'selected' : '' }}>🌐 Cáp quang Internet tổng tòa nhà</option>
                                <option value="waste_collection" {{ old('expense_type', $expense->expense_type) == 'waste_collection' ? 'selected' : '' }}>🗑️ Phí rác thải môi trường đô thị</option>
                                <option value="maintenance_repair" {{ old('expense_type', $expense->expense_type) == 'maintenance_repair' ? 'selected' : '' }}>🛠️ Bảo trì thang máy, máy bơm, sửa chữa chung</option>
                                <option value="other" {{ old('expense_type', $expense->expense_type) == 'other' ? 'selected' : '' }}>Chi phí vận hành khác</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tiêu đề khoản chi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required value="{{ old('title', $expense->title) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số tiền thực tế đã chi (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-lg fw-bold text-danger" required min="0" step="1000" value="{{ old('amount', $expense->amount) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chỉ số tiêu thụ trên đồng hồ tổng (đối soát)</label>
                            <input type="number" step="0.1" name="total_meter_usage" class="form-control" value="{{ old('total_meter_usage', $expense->total_meter_usage) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày thanh toán <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" required value="{{ old('payment_date', $expense->payment_date->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Người thực hiện chi</label>
                            <input type="text" name="paid_by" class="form-control" value="{{ old('paid_by', $expense->paid_by) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ghi chú chi tiết</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Cập Nhật Khoản Chi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
