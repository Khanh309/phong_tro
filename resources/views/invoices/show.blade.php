@extends('layouts.app')

@section('title', 'Hóa Đơn #' . $invoice->invoice_code)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0">Hóa Đơn</h3>
            <span class="badge bg-dark font-monospace fs-5 px-3 py-2"><i class="bi bi-receipt me-1"></i>{{ $invoice->invoice_code }}</span>
            <span class="badge bg-{{ $invoice->status_badge }}-subtle text-{{ $invoice->status_badge }} fs-6">
                {{ $invoice->status_label }}
            </span>
        </div>
        <p class="text-muted mb-0">Phòng {{ $invoice->room->room_number }} - <b>{{ $invoice->room->property->name }}</b> (Kỳ: Tháng {{ $invoice->month }}/{{ $invoice->year }})</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" onclick="copyZaloModal()">
            <i class="bi bi-chat-dots-fill me-1"></i> Soạn Tin Nhắn Zalo
        </button>
        <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> In Hóa Đơn
        </a>
        @if($invoice->status !== 'paid')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-cash-coin me-1"></i> Ghi Nhận Thanh Toán
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: BẢNG CHI TIẾT TÍNH TIỀN -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-receipt text-primary me-2"></i>Bảng Kê Chi Tiết Tiền Nhà & Dịch Vụ</span>
                <span class="text-muted small">Hạn nộp: <b class="text-danger">{{ $invoice->due_date->format('d/m/Y') }}</b></span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Khoản Mục</th>
                            <th>Chi Tiết Số Liệu</th>
                            <th class="text-end" style="width: 150px;">Thành Tiền (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Tiền phòng -->
                        <tr>
                            <td class="fw-semibold">1. Tiền thuê phòng trọ</td>
                            <td class="text-muted small">Tháng {{ $invoice->month }}/{{ $invoice->year }}</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->room_price, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- 2. Tiền điện -->
                        <tr>
                            <td class="fw-semibold">2. Tiền điện sinh hoạt</td>
                            <td class="small">
                                Chỉ số: <b>{{ $invoice->electricity_old }}</b> → <b>{{ $invoice->electricity_new }}</b>
                                <br>Tiêu thụ: <b>{{ $invoice->electricity_usage }} kWh</b> x {{ number_format($invoice->electricity_rate) }}đ
                            </td>
                            <td class="text-end fw-bold text-warning-emphasis">{{ number_format($invoice->electricity_total, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- 3. Tiền nước -->
                        <tr>
                            <td class="fw-semibold">3. Tiền nước sinh hoạt</td>
                            <td class="small">
                                @php
                                    $wType = $invoice->water_calculation_type ?? $invoice->room->water_calculation_type;
                                @endphp
                                @if($wType === 'per_person')
                                    <span class="badge bg-info-subtle text-info border">👥 Tính theo đầu người</span>
                                    <div class="text-dark fw-semibold mt-1">
                                        <b>{{ $invoice->water_usage }} người</b> × {{ number_format($invoice->water_rate) }}đ/người
                                    </div>
                                @elseif($wType === 'fixed_room')
                                    <span class="badge bg-primary-subtle text-primary border">🏠 Khoán theo phòng</span>
                                    <div class="text-muted mt-1">
                                        Cố định 1 phòng: {{ number_format($invoice->water_rate) }}đ
                                    </div>
                                @else
                                    Chỉ số: <b>{{ $invoice->water_old }}</b> → <b>{{ $invoice->water_new }}</b>
                                    <br>Tiêu thụ: <b>{{ $invoice->water_usage }} m³</b> × {{ number_format($invoice->water_rate) }}đ/m³
                                @endif
                            </td>
                            <td class="text-end fw-bold text-info-emphasis">{{ number_format($invoice->water_total, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- 4. Các phí dịch vụ -->
                        @if(!empty($invoice->fees_detail) && is_array($invoice->fees_detail))
                            @foreach($invoice->fees_detail as $fee)
                                <tr>
                                    <td class="fw-semibold">{{ $fee['name'] }}</td>
                                    <td class="text-muted small">
                                        {{ $fee['calc_desc'] ?? 'Phí dịch vụ phòng' }}
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($fee['amount'], 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        @endif

                        @if($invoice->discount > 0)
                            <tr class="table-success">
                                <td class="fw-semibold text-success">Giảm trừ / Khuyến mại</td>
                                <td class="text-muted small">Miễn giảm trực tiếp</td>
                                <td class="text-end fw-bold text-success">-{{ number_format($invoice->discount, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif

                        <!-- TỔNG CỘNG -->
                        <tr class="table-light fs-5">
                            <td colspan="2" class="fw-bold text-uppercase">TỔNG CỘNG TIỀN PHẢI NỘP</td>
                            <td class="text-end fw-bold text-primary">{{ number_format($invoice->total_amount, 0, ',', '.') }}đ</td>
                        </tr>

                        <!-- ĐÃ TRẢ & CÒN LẠI -->
                        <tr>
                            <td colspan="2" class="text-muted">Đã thanh toán:</td>
                            <td class="text-end fw-bold text-success">{{ number_format($invoice->paid_amount, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr class="table-danger fs-5">
                            <td colspan="2" class="fw-bold text-danger">CÒN PHẢI THANH TOÁN:</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($invoice->remaining_amount, 0, ',', '.') }}đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($invoice->notes)
                <div class="card-footer bg-light small">
                    <span class="fw-bold">Lịch sử thanh toán & Ghi chú:</span>
                    <div style="white-space: pre-line;">{{ $invoice->notes }}</div>
                </div>
            @endif
        </div>

        <!-- Ý KIẾN / KHIẾU NẠI CỦA KHÁCH THUÊ CHO HÓA ĐƠN NÀY -->
        <div class="card shadow-sm border-info mb-4">
            <div class="card-header bg-info-subtle text-info-emphasis d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-chat-left-dots-fill me-2"></i>Ý Kiến / Khiếu Nại Của Khách Thuê</span>
                <span class="badge bg-info text-dark">{{ $invoice->feedbacks->count() }} ý kiến</span>
            </div>
            <div class="card-body p-0">
                @forelse($invoice->feedbacks as $fb)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-info-subtle text-info">{{ $fb->feedback_type_name }}</span>
                                <span class="text-muted small ms-2">{{ $fb->created_at->format('d/m/Y H:i') }} bởi <b>{{ $fb->tenant->name }}</b></span>
                            </div>
                            <span class="badge bg-{{ $fb->status_badge }}-subtle text-{{ $fb->status_badge }} fw-bold">
                                {{ $fb->status_label }}
                            </span>
                        </div>
                        <div class="p-2 bg-light rounded text-dark small mb-2">
                            <b>Ý kiến khách gửi:</b> "{{ $fb->content }}"
                        </div>

                        @if($fb->admin_reply)
                            <div class="p-2 bg-success-subtle rounded text-success-emphasis small mb-2">
                                <i class="bi bi-reply-fill me-1"></i><b>Chủ nhà đã trả lời:</b> "{{ $fb->admin_reply }}"
                                @if($fb->resolved_at)
                                    <span class="text-muted small">({{ $fb->resolved_at->format('d/m/Y H:i') }})</span>
                                @endif
                            </div>
                        @endif

                        <!-- Form phản hồi nhanh -->
                        <form action="{{ route('feedbacks.reply', $fb->id) }}" method="POST" class="mt-2 bg-white p-2 border rounded">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-4">
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="processing" {{ $fb->status === 'processing' ? 'selected' : '' }}>⏳ Đang xử lý</option>
                                        <option value="resolved" {{ $fb->status === 'resolved' ? 'selected' : '' }}>✅ Đã giải quyết</option>
                                        <option value="rejected" {{ $fb->status === 'rejected' ? 'selected' : '' }}>❌ Từ chối khiếu nại</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="text" name="admin_reply" class="form-control form-control-sm" required placeholder="Nhập câu trả lời cho khách..." value="{{ $fb->admin_reply }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-send-fill"></i> Gửi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted small">
                        <i class="bi bi-check-circle text-success me-1"></i> Không có khiếu nại hay thắc mắc nào cho hóa đơn này.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: MÃ VIETQR TỰ ĐỘNG & THÔNG TIN CHUYỂN KHOẢN -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-primary mb-4 text-center">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-qr-code-scan me-2"></i>Quét Mã VietQR Chuyển Khoản</h5>
            </div>
            <div class="card-body p-4">
                @if($invoice->viet_qr_url && $invoice->remaining_amount > 0)
                    <div class="mb-3">
                        <img src="{{ $invoice->viet_qr_url }}" alt="Mã VietQR Chuyển Khoản" class="img-fluid rounded border shadow-sm" style="max-height: 280px;">
                    </div>
                    <div class="small text-muted mb-2">Khách mở App ngân hàng quét mã để thanh toán đúng số tiền và nội dung</div>
                @elseif($invoice->status === 'paid')
                    <div class="py-5 text-success">
                        <i class="bi bi-check-circle-fill fs-1 d-block mb-2"></i>
                        <h5 class="fw-bold">HÓA ĐƠN ĐÃ THANH TOÁN ĐẦY ĐỦ</h5>
                        <div class="small text-muted">Thời điểm: {{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : '' }}</div>
                    </div>
                @else
                    <div class="alert alert-warning small">
                        Chưa cấu hình thông tin ngân hàng trong cài đặt Nhà trọ ({{ $invoice->room->property->name }}).
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
                        <span class="text-muted">Số tiền:</span>
                        <span class="fw-bold text-danger">{{ number_format($invoice->remaining_amount, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Nội dung CK:</span>
                        <code class="fw-bold">P{{ $invoice->room->room_number }} T{{ $invoice->month }} {{ $invoice->year }}</code>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('portal.show', $invoice->invoice_code) }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                        <i class="bi bi-link-45deg"></i> Xem giao diện khách thuê (Cổng Online)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL GHI NHẬN THANH TOÁN -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('invoices.payment', $invoice->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2"></i>Ghi Nhận Thanh Toán Hóa Đơn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số tiền khách thanh toán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="payment_amount" class="form-control form-control-lg fw-bold text-success" required min="1" max="{{ $invoice->remaining_amount }}" value="{{ $invoice->remaining_amount }}">
                        <div class="form-text">Số tiền còn nợ: <b>{{ number_format($invoice->remaining_amount, 0, ',', '.') }}đ</b></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hình thức thanh toán <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="transfer">Chuyển khoản Ngân hàng (VietQR)</option>
                            <option value="cash">Tiền mặt tại phòng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày nhận tiền <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required value="{{ now()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú thanh toán</label>
                        <input type="text" name="payment_note" class="form-control" placeholder="vd: Khách trả trước một nửa...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Xác Nhận Đã Thu Tiền</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL XEM & COPY TIN NHẮN ZALO -->
<div class="modal fade" id="zaloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-chat-dots-fill me-2"></i> Mẫu Tin Nhắn Nhắc Nộp Tiền Zalo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea id="zaloMessageContent" class="form-control" rows="12" readonly style="font-family: monospace; font-size: 0.88rem;">{{ $invoice->generateZaloMessage() }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                @if($invoice->contract?->tenant?->phone)
                    <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $invoice->contract->tenant->phone) }}" target="_blank" class="btn btn-outline-success">
                        <i class="bi bi-send-fill me-1"></i> Mở Zalo Khách
                    </a>
                @endif
                <button type="button" class="btn btn-success" onclick="copyZaloContent()">
                    <i class="bi bi-clipboard-check me-1"></i> Sao chép tin nhắn
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyZaloModal() {
        var modal = new bootstrap.Modal(document.getElementById('zaloModal'));
        modal.show();
    }

    function copyZaloContent() {
        var textarea = document.getElementById('zaloMessageContent');
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(textarea.value).then(function() {
            alert('Đã sao chép tin nhắn Zalo vào bộ nhớ tạm!');
        });
    }
</script>
@endpush
