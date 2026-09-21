@extends('layouts.app')

@section('title', 'Lập Hợp Đồng Thuê Phòng Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Ký Kết Hợp Đồng Cho Thuê Phòng</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('contracts.store') }}" method="POST">
                    @csrf

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Chọn Phòng Trọ & Khách Thuê</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phòng Cho Thuê (Chỉ hiển thị phòng trống) <span class="text-danger">*</span></label>
                            <select name="room_id" id="roomSelect" class="form-select" required onchange="updateRoomPrice()">
                                <option value="">-- Chọn phòng trọ còn trống --</option>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}" data-price="{{ $r->price }}" {{ (old('room_id') ?? $selectedRoomId) == $r->id ? 'selected' : '' }}>
                                        Phòng {{ $r->room_number }} (Tầng {{ $r->floor }}) - {{ $r->property->name }} - {{ number_format($r->price) }}đ
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Khách Thuê Đại Diện Ký HĐ <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="tenant_id" class="form-select" required>
                                    <option value="">-- Chọn hồ sơ khách thuê --</option>
                                    @foreach($tenants as $t)
                                        <option value="{{ $t->id }}" {{ old('tenant_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->name }} ({{ $t->phone }}) - {{ $t->hometown }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('tenants.create') }}" target="_blank" class="btn btn-outline-secondary" title="Thêm khách mới nếu chưa có"><i class="bi bi-plus-lg"></i></a>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Thời Hạn Thuê & Tiền Cọc</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu thuê <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày kết thúc hợp đồng <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required value="{{ old('end_date', now()->addMonths(6)->toDateString()) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giá thuê thỏa thuận (VNĐ/tháng) <span class="text-danger">*</span></label>
                            <input type="number" name="rental_price" id="rentalPriceInput" class="form-control" required min="0" step="50000" value="{{ old('rental_price') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tiền đặt cọc giữ chân (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="deposit_amount" id="depositAmountInput" class="form-control" required min="0" step="50000" placeholder="Thường bằng 1 tháng tiền phòng" value="{{ old('deposit_amount') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Trạng thái tiền cọc</label>
                            <select name="deposit_status" class="form-select">
                                <option value="held" {{ old('deposit_status') == 'held' ? 'selected' : '' }}>Chủ nhà đang giữ đủ tiền cọc</option>
                                <option value="refunded" {{ old('deposit_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn trả</option>
                                <option value="deducted" {{ old('deposit_status') == 'deducted' ? 'selected' : '' }}>Đã cấn trừ</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Điều khoản bổ sung / Nội quy phòng trọ</label>
                            <textarea name="terms" class="form-control" rows="4" placeholder="Giờ giấc ra vào, giữ gìn vệ sinh chung, quy định không nấu nướng trong phòng ngủ, trách nhiệm bảo quản tài sản...">{{ old('terms', "1. Bên B có trách nhiệm thanh toán tiền thuê phòng và các dịch vụ điện, nước đầy đủ từ ngày 01 đến ngày 05 hàng tháng.\n2. Giữ gìn an ninh trật tự, khóa cổng sau 23h00.\n3. Không được tự ý đục phá, sửa đổi kết cấu phòng và chịu trách nhiệm bồi thường nếu làm hư hỏng thiết bị bàn giao.") }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Hoàn Tất Ký Hợp Đồng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateRoomPrice() {
        var select = document.getElementById('roomSelect');
        var selectedOption = select.options[select.selectedIndex];
        var price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('rentalPriceInput').value = price;
            document.getElementById('depositAmountInput').value = price;
        }
    }
    // Chạy khi tải trang nếu đã chọn sẵn phòng
    document.addEventListener("DOMContentLoaded", function() {
        if (document.getElementById('roomSelect').value && !document.getElementById('rentalPriceInput').value) {
            updateRoomPrice();
        }
    });
</script>
@endpush
