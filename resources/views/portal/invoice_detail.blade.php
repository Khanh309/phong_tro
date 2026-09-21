@extends('layouts.tenant')

@section('title', 'Chi Tiết Hóa Đơn #' . $invoice->invoice_code)

@section('content')
<div class="mb-3 no-print">
    <a href="{{ route('portal.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại Bảng Điều Khiển Khách Thuê
    </a>
</div>

<!-- HEADER HÓA ĐƠN -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h3 class="fw-bold text-dark mb-0">Hóa Đơn Phòng {{ $invoice->room->room_number }}</h3>
            <span class="badge bg-dark font-monospace fs-5 px-3 py-2"><i class="bi bi-receipt me-1"></i>{{ $invoice->invoice_code }}</span>
            <span class="badge bg-{{ $invoice->status_badge }}-subtle text-{{ $invoice->status_badge }} fs-6">
                {{ $invoice->status_label }}
            </span>
        </div>
        <p class="text-muted mb-0 mt-1">
            Kỳ thanh toán: <b>Tháng {{ $invoice->month }}/{{ $invoice->year }}</b> | Hạn nộp: <b class="text-danger">{{ $invoice->due_date->format('d/m/Y') }}</b>
        </p>
    </div>

    <div class="d-flex gap-2 no-print">
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> In Bảng Kê
        </button>
        <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="openFeedbackModal('{{ $invoice->id }}', '{{ $invoice->invoice_code }}')">
            <i class="bi bi-chat-square-dots me-1"></i> Khiếu Nại / Góp Ý Về Hóa Đơn Này
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: BẢNG KÊ CHI TIẾT TIỀN NHÀ & DỊCH VỤ -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-dark"><i class="bi bi-receipt text-primary me-2"></i>Bảng Kê Chi Tiết Tiền Nhà & Điện Nước</span>
                <span class="text-muted small">Phòng {{ $invoice->room->room_number }} - {{ $invoice->room->property->name }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Khoản Mục</th>
                            <th>Chi Tiết Số Liệu</th>
                            <th class="text-end" style="width: 160px;">Thành Tiền (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Tiền phòng -->
                        <tr>
                            <td class="fw-bold">1. Tiền thuê phòng trọ</td>
                            <td class="text-muted small">Kỳ: Tháng {{ $invoice->month }}/{{ $invoice->year }}</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->room_price, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- 2. Tiền điện -->
                        <tr>
                            <td class="fw-bold">2. Tiền điện sinh hoạt</td>
                            <td class="small">
                                Chỉ số: <b>{{ $invoice->electricity_old }}</b> → <b>{{ $invoice->electricity_new }}</b>
                                <div class="text-muted">Tiêu thụ: <b>{{ $invoice->electricity_usage }} kWh</b> x {{ number_format($invoice->electricity_rate, 0, ',', '.') }}đ</div>
                            </td>
                            <td class="text-end fw-bold text-warning-emphasis">{{ number_format($invoice->electricity_total, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- 3. Tiền nước -->
                        <tr>
                            <td class="fw-bold">3. Tiền nước sinh hoạt</td>
                            <td class="small">
                                Chỉ số: <b>{{ $invoice->water_old }}</b> → <b>{{ $invoice->water_new }}</b>
                                <div class="text-muted">Sử dụng: <b>{{ $invoice->water_usage }} số</b> x {{ number_format($invoice->water_rate, 0, ',', '.') }}đ</div>
                            </td>
                            <td class="text-end fw-bold text-info-emphasis">{{ number_format($invoice->water_total, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- 4. Các loại phí dịch vụ đi kèm -->
                        @if(!empty($invoice->fees_detail))
                            @foreach($invoice->fees_detail as $idx => $fee)
                                <tr>
                                    <td class="fw-semibold">{{ 4 + $idx }}. {{ $fee['name'] }}</td>
                                    <td class="text-muted small">Phí dịch vụ phòng</td>
                                    <td class="text-end fw-bold">{{ number_format($fee['amount'], 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Giảm trừ nếu có -->
                        @if($invoice->discount > 0)
                            <tr class="text-success">
                                <td class="fw-semibold">Giảm trừ khuyến mại</td>
                                <td class="small">Chính sách ưu đãi</td>
                                <td class="text-end fw-bold">-{{ number_format($invoice->discount, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif

                        <!-- TỔNG CỘNG -->
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold text-uppercase fs-6">TỔNG TIỀN HÓA ĐƠN:</td>
                            <td class="text-end fw-bold text-dark fs-5">{{ number_format($invoice->total_amount, 0, ',', '.') }}đ</td>
                        </tr>

                        @if($invoice->paid_amount > 0)
                            <tr class="table-success">
                                <td colspan="2" class="fw-semibold text-success">Đã thanh toán:</td>
                                <td class="text-end fw-bold text-success">-{{ number_format($invoice->paid_amount, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif

                        <tr class="{{ $invoice->remaining_amount > 0 ? 'table-danger' : 'table-light' }}">
                            <td colspan="2" class="fw-bold fs-6 text-danger text-uppercase">CÒN PHẢI NỘP:</td>
                            <td class="text-end fw-bold text-danger fs-4">{{ number_format($invoice->remaining_amount, 0, ',', '.') }}đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($invoice->notes)
                <div class="card-footer bg-light small">
                    <span class="fw-bold">Ghi chú từ Chủ nhà:</span>
                    <div style="white-space: pre-line;" class="text-muted">{{ $invoice->notes }}</div>
                </div>
            @endif
        </div>

        <!-- LỊCH SỬ KHIẾU NẠI CHO HÓA ĐƠN NÀY -->
        <div class="card shadow-sm border-info mb-4">
            <div class="card-header bg-info-subtle text-info-emphasis d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold"><i class="bi bi-chat-left-dots-fill me-2"></i>Ý Kiến / Khiếu Nại Của Bạn Cho Hóa Đơn Này</span>
                <span class="badge bg-info text-dark">{{ $invoice->feedbacks->count() }} ý kiến</span>
            </div>
            <div class="card-body p-0">
                @forelse($invoice->feedbacks as $fb)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-info-subtle text-info">{{ $fb->feedback_type_name }}</span>
                                <span class="text-muted small ms-2">{{ $fb->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="badge bg-{{ $fb->status_badge }}-subtle text-{{ $fb->status_badge }} fw-bold">
                                {{ $fb->status_label }}
                            </span>
                        </div>
                        <div class="p-2 bg-light rounded text-dark small mb-2">
                            <b>Bạn viết:</b> "{{ $fb->content }}"
                        </div>
                        @if($fb->admin_reply)
                            <div class="p-2 bg-success-subtle rounded text-success-emphasis small">
                                <i class="bi bi-reply-fill me-1"></i><b>Chủ nhà đã phản hồi:</b> "{{ $fb->admin_reply }}"
                            </div>
                        @else
                            <div class="small text-muted fst-italic">
                                <i class="bi bi-hourglass-split"></i> Đang chờ Chủ nhà kiểm tra và phản hồi...
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted small">
                        Bạn chưa gửi thắc mắc hay khiếu nại nào về hóa đơn này.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: MÃ VIETQR ĐỘNG & THÔNG TIN CHUYỂN KHOẢN -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-primary mb-4 text-center">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-qr-code-scan me-2"></i>Quét Mã VietQR Thanh Toán</h5>
            </div>
            <div class="card-body p-4">
                @if($invoice->remaining_amount > 0 && $invoice->viet_qr_url)
                    <div class="mb-3">
                        <img src="{{ $invoice->viet_qr_url }}" alt="Mã VietQR Chuyển Khoản" class="img-fluid rounded border shadow-sm p-1" style="max-height: 260px;">
                    </div>
                    <div class="small text-muted mb-3">
                        Mở App Ngân Hàng quét mã để chuyển khoản chính xác số tiền & nội dung:
                    </div>
                @elseif($invoice->status === 'paid')
                    <div class="py-5 text-success">
                        <i class="bi bi-check-circle-fill fs-1 d-block mb-2"></i>
                        <h5 class="fw-bold">BẠN ĐÃ THANH TOÁN ĐỦ HÓA ĐƠN NÀY</h5>
                        <div class="small text-muted">Cảm ơn bạn đã luôn đóng tiền phòng đúng hạn!</div>
                    </div>
                @endif

                <div class="bg-light p-3 rounded text-start small border">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Ngân hàng:</span>
                        <span class="fw-bold text-dark">{{ $invoice->room->property->bank_name ?: '---' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Số tài khoản:</span>
                        <code class="fw-bold fs-6">{{ $invoice->room->property->bank_account_number ?: '---' }}</code>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Chủ tài khoản:</span>
                        <span class="fw-bold text-uppercase">{{ $invoice->room->property->bank_account_holder ?: '---' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Số tiền cần trả:</span>
                        <span class="fw-bold text-danger">{{ number_format($invoice->remaining_amount, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Nội dung CK:</span>
                        <code class="fw-bold">P{{ $invoice->room->room_number }} T{{ $invoice->month }} {{ $invoice->year }}</code>
                    </div>
                </div>

                <div class="alert alert-light border small mt-3 text-start mb-0">
                    <i class="bi bi-telephone-fill text-primary me-1"></i>
                    Liên hệ Chủ nhà / Quản lý: <b>{{ $invoice->room->property->bank_account_holder }}</b>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL GỬI KHIẾU NẠI / Ý KIẾN VỀ HÓA ĐƠN -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="feedbackForm" action="/khach-thue/feedback/{{ $invoice->id }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-chat-square-dots me-2"></i>Khiếu Nại / Ý Kiến Hóa Đơn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border small mb-3">
                        Đang khiếu nại hóa đơn: <b class="text-danger font-monospace">{{ $invoice->invoice_code }}</b>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nội dung thắc mắc về: <span class="text-danger">*</span></label>
                        <select name="feedback_type" class="form-select" required>
                            <option value="electricity">⚡ Thắc mắc về chỉ số Điện (Điện tăng bất thường / nghi ngờ đồng hồ)</option>
                            <option value="water">💧 Thắc mắc về chỉ số Nước</option>
                            <option value="fees">🏷️ Thắc mắc về các khoản phí dịch vụ (Wifi, rác, gửi xe...)</option>
                            <option value="other">Ý kiến / Thắc mắc khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chi tiết ý kiến của bạn: <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="Mô tả cụ thể thắc mắc của bạn để Chủ nhà kiểm tra lại..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Gửi Ý Kiến Đến Chủ Nhà
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openFeedbackModal(invoiceId, invoiceCode) {
        var modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        modal.show();
    }
</script>
@endpush
