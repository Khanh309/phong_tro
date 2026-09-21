@extends('layouts.app')

@section('title', 'Thanh Lý & Trả Phòng ' . $contract->room->room_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-box-arrow-right me-2"></i>Quy Trình Trả Phòng & Quyết Toán Tiền Cọc</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border mb-4">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted small">Phòng trả:</span>
                            <div class="fw-bold fs-5">Phòng {{ $contract->room->room_number }} - {{ $contract->room->property->name }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted small">Khách trả phòng:</span>
                            <div class="fw-bold fs-5">{{ $contract->tenant->name }} ({{ $contract->tenant->phone }})</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('contracts.checkout.process', $contract->id) }}" method="POST" id="checkoutForm">
                    @csrf

                    <h6 class="text-danger fw-bold mb-3 border-bottom pb-2">1. Chốt Số Điện & Nước Ngày Cuối</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chỉ số điện chốt ngày trả (kWh) <span class="text-danger">*</span></label>
                            <input type="number" name="final_electricity" class="form-control" required min="{{ $latestElec }}" step="0.1" value="{{ old('final_electricity', $latestElec) }}">
                            <div class="form-text">Chỉ số điện trước đó: <b>{{ $latestElec }} kWh</b></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chỉ số nước chốt ngày trả (m³) <span class="text-danger">*</span></label>
                            <input type="number" name="final_water" class="form-control" required min="{{ $latestWater }}" step="0.1" value="{{ old('final_water', $latestWater) }}">
                            <div class="form-text">Chỉ số nước trước đó: <b>{{ $latestWater }} m³</b></div>
                        </div>
                    </div>

                    <h6 class="text-danger fw-bold mb-3 border-bottom pb-2">2. Quyết Toán Khấu Trừ & Hoàn Cọc</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tiền cọc ban đầu đang giữ</label>
                            <input type="text" class="form-control fw-bold text-success bg-light" readonly id="depositHeld" value="{{ number_format($contract->deposit_amount, 0, ',', '.') }}đ">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Khấu trừ hư hỏng đồ đạc (VNĐ)</label>
                            <input type="number" name="damage_deduction" id="damageInput" class="form-control" min="0" step="50000" value="0" oninput="calculateRefund()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Khấu trừ nợ tiền phòng/dịch vụ (VNĐ)</label>
                            <input type="number" name="unpaid_bills_deduction" id="unpaidInput" class="form-control" min="0" step="50000" value="0" oninput="calculateRefund()">
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted fw-bold d-block">SỐ TIỀN CỌC THỰC TẾ CẦN HOÀN TRẢ KHÁCH:</span>
                                    <small class="text-muted">Tiền cọc - Hư hỏng - Tiền nợ phòng</small>
                                </div>
                                <h3 class="fw-bold text-primary mb-0" id="refundDisplay">{{ number_format($contract->deposit_amount, 0, ',', '.') }}đ</h3>
                                <input type="hidden" name="refund_amount" id="refundAmount" value="{{ $contract->deposit_amount }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Chuyển trạng thái phòng sau khi trả <span class="text-danger">*</span></label>
                            <select name="room_next_status" class="form-select">
                                <option value="available">🟢 Phòng trống (Sẵn sàng cho khách mới thuê ngay)</option>
                                <option value="maintenance" selected>🟡 Đang dọn dẹp / Sơn sửa bảo trì trước khi đón khách mới</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ghi chú quyết toán trả phòng</label>
                            <textarea name="checkout_note" class="form-control" rows="3" placeholder="Ghi chú về tình trạng bàn giao chìa khóa, hiện trạng phòng, số tiền đã chuyển khoản trả cọc..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold" onclick="return confirm('Xác nhận thanh lý hợp đồng và trả phòng này?')">
                            <i class="bi bi-check-circle-fill me-1"></i> Xác Nhận Trả Phòng & Quyết Toán
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
    var originalDeposit = {{ (float) $contract->deposit_amount }};

    function calculateRefund() {
        var damage = parseFloat(document.getElementById('damageInput').value) || 0;
        var unpaid = parseFloat(document.getElementById('unpaidInput').value) || 0;
        var refund = originalDeposit - damage - unpaid;

        document.getElementById('refundAmount').value = refund;
        document.getElementById('refundDisplay').innerText = new Intl.NumberFormat('vi-VN').format(refund) + 'đ';
        if (refund < 0) {
            document.getElementById('refundDisplay').className = 'fw-bold text-danger mb-0';
        } else {
            document.getElementById('refundDisplay').className = 'fw-bold text-primary mb-0';
        }
    }
</script>
@endpush
