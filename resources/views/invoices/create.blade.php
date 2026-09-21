@extends('layouts.app')

@section('title', 'Lập Hóa Đơn Lẻ')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-primary me-2"></i>Lập Hóa Đơn Thu Tiền Phòng</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Chọn Phòng & Kỳ Hóa Đơn</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phòng tính tiền <span class="text-danger">*</span></label>
                            <select name="room_id" id="roomSelect" class="form-select" required onchange="window.location.href='{{ route('invoices.create') }}?room_id=' + this.value">
                                <option value="">-- Chọn phòng đang thuê --</option>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}" {{ ($room && $room->id == $r->id) ? 'selected' : '' }}>
                                        Phòng {{ $r->room_number }} - {{ $r->property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tháng</label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Năm</label>
                            <select name="year" class="form-select" required>
                                @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tiền phòng cơ bản (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="room_price" class="form-control" required value="{{ $room ? ($room->currentContract->rental_price ?? $room->price) : 3000000 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hạn thanh toán <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control" required value="{{ now()->copy()->startOfMonth()->addDays(5)->toDateString() }}">
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Chỉ Số Điện & Nước</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số điện cũ (kWh) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="electricity_old" class="form-control bg-light" required value="{{ $defaultOldElec }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số điện mới (kWh) <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="electricity_new" class="form-control fw-bold border-primary" required min="{{ $defaultOldElec }}" value="{{ $defaultOldElec + 120 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá điện (VNĐ/kWh) <span class="text-danger">*</span></label>
                            <input type="number" name="electricity_rate" class="form-control" required value="{{ $room->electricity_rate ?? 3800 }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số nước cũ <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="water_old" class="form-control bg-light" required value="{{ $defaultOldWater }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Chỉ số nước mới <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="water_new" class="form-control fw-bold border-info" required min="{{ $defaultOldWater }}" value="{{ $defaultOldWater + 7 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá nước (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="water_rate" class="form-control" required value="{{ $room->water_rate ?? 30000 }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giảm giá / Miễn trừ (nếu có)</label>
                            <input type="number" name="discount" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ghi chú hóa đơn</label>
                            <input type="text" name="notes" class="form-control" placeholder="Ghi chú chuyển khoản...">
                        </div>
                    </div>

                    @if($room && $room->fees->isNotEmpty())
                        <div class="p-3 bg-light rounded mb-4">
                            <div class="fw-bold small text-muted mb-2">Các khoản phí dịch vụ đi kèm của phòng sẽ được tự động tính:</div>
                            <ul class="mb-0 small text-muted">
                                @foreach($room->fees as $fee)
                                    <li>{{ $fee->fee_name }}: {{ number_format($fee->unit_price) }}đ ({{ $fee->fee_type }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-receipt me-1"></i> Phát Hành Hóa Đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
