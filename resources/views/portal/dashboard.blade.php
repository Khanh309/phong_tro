@extends('layouts.tenant')

@section('title', 'Bảng Điều Khiển Khách Thuê - ' . $tenant->name)

@section('content')
<!-- THÔNG TIN TỔNG QUAN PHÒNG & HỢP ĐỒNG -->
<div class="row g-3 mb-4">
    <!-- Thẻ Phòng & Hợp đồng -->
    <div class="col-12 col-lg-8">
        <div class="card p-4 h-100 bg-white shadow-sm border-start border-4 border-primary">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 mb-1">CĂN HỘ CỦA BẠN</span>
                    <h3 class="fw-bold text-dark mb-0">Phòng {{ $room->room_number ?? '---' }}</h3>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $room->property->name ?? '' }} ({{ $room->property->address ?? '' }})
                    </div>
                </div>
                <div class="text-end">
                    <span class="text-muted small">GIÁ THUÊ PHÒNG</span>
                    <div class="h4 fw-bold text-primary mb-0">
                        {{ number_format($tenant->currentContract->rental_price ?? $room->price ?? 0) }}đ
                        <small class="fs-6 text-muted fw-normal">/tháng</small>
                    </div>
                </div>
            </div>

            <div class="row g-3 pt-3 border-top small text-muted">
                <div class="col-6 col-md-3">
                    <div>Tiền cọc giữ:</div>
                    <b class="text-dark fs-6">{{ number_format($tenant->currentContract->deposit_amount ?? 0) }}đ</b>
                </div>
                <div class="col-6 col-md-3">
                    <div>Ngày nhận phòng:</div>
                    <b class="text-dark">{{ $tenant->currentContract?->start_date?->format('d/m/Y') ?? '---' }}</b>
                </div>
                <div class="col-6 col-md-3">
                    <div>Hạn hợp đồng:</div>
                    <b class="text-dark">{{ $tenant->currentContract?->end_date?->format('d/m/Y') ?? '---' }}</b>
                </div>
                <div class="col-6 col-md-3">
                    <div>Thời hạn còn:</div>
                    @if($tenant->currentContract)
                        @php $daysLeft = (int) now()->diffInDays($tenant->currentContract->end_date, false); @endphp
                        <b class="{{ $daysLeft < 30 ? 'text-danger' : 'text-success' }}">{{ $daysLeft > 0 ? $daysLeft . ' ngày' : 'Đã hết hạn' }}</b>
                    @else
                        <b>---</b>
                    @endif
                </div>
            </div>

            <!-- CÁC KHOẢN PHÍ DỊCH VỤ CỦA PHÒNG BẠN -->
            <div class="mt-3 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark small">
                        <i class="bi bi-tags-fill text-primary me-1"></i>Biểu phí dịch vụ của phòng:
                    </span>
                    <span class="badge bg-light text-dark border small">⚡ Điện: {{ number_format($room->electricity_rate ?? 0) }}đ/kWh | 💧 Nước: {{ number_format($room->water_rate ?? 0) }}đ</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($room && $room->fees->isNotEmpty())
                        @foreach($room->fees as $fee)
                            <div class="border rounded px-2 py-1 bg-light small d-flex align-items-center gap-2">
                                <span class="text-dark fw-semibold">{{ $fee->fee_name }}:</span>
                                <b class="text-primary">{{ number_format($fee->unit_price, 0, ',', '.') }}đ</b>
                                <span class="text-muted" style="font-size: 0.7rem;">({{ $fee->fee_type === 'fixed' ? 'cố định' : ($fee->fee_type === 'per_person' ? 'theo người' : 'theo xe') }})</span>
                            </div>
                        @endforeach
                    @else
                        <span class="text-muted small fst-italic">Phòng chưa có khoản phí dịch vụ riêng cố định.</span>
                    @endif
                </div>
            <!-- CÁC THÀNH VIÊN ĐANG Ở CÙNG PHÒNG -->
            @if($tenant->currentContract)
                @php $members = $tenant->currentContract->members ?? collect(); @endphp
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small">
                            <i class="bi bi-people-fill text-success me-1"></i>Khách đang ở trong căn hộ ({{ 1 + $members->count() }} người):
                        </span>
                        <span class="badge bg-success-subtle text-success small">Tối đa: {{ $room->max_tenants ?? 2 }} người</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="border rounded px-2 py-1 bg-white small border-primary">
                            <span class="text-primary fw-bold"><i class="bi bi-person-fill-check me-1"></i>{{ $tenant->name }}</span>
                            <span class="badge bg-primary text-white ms-1" style="font-size: 0.65rem;">Đại diện HĐ</span>
                        </div>
                        @forelse($members as $mem)
                            <div class="border rounded px-2 py-1 bg-light small">
                                <span class="text-dark fw-semibold">• {{ $mem->name }}</span>
                                <span class="text-muted" style="font-size: 0.7rem;">({{ $mem->relationship ?: 'ở cùng' }})</span>
                                @if($mem->vehicle_plate)
                                    <span class="text-secondary ms-1" style="font-size: 0.7rem;"><i class="bi bi-bicycle"></i> {{ $mem->vehicle_plate }}</span>
                                @endif
                            </div>
                        @empty
                            <span class="text-muted small fst-italic">(Chưa đăng ký thêm người ở cùng)</span>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Thẻ Tóm tắt Tài chính -->
    <div class="col-12 col-lg-4">
        <div class="card p-4 h-100 {{ $overdueInvoices->isNotEmpty() ? 'border-danger bg-danger-subtle' : 'bg-white' }} shadow-sm">
            @if($overdueInvoices->isNotEmpty())
                <div class="d-flex align-items-center gap-2 text-danger fw-bold mb-2">
                    <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                    <span>CẢNH BÁO QUÁ HẠN</span>
                </div>
                <h3 class="fw-bold text-danger mb-1">{{ number_format($overdueInvoices->sum('remaining_amount')) }}đ</h3>
                <p class="small text-danger mb-3">Bạn có {{ $overdueInvoices->count() }} hóa đơn bị quá hạn. Vui lòng thanh toán sớm để tránh gián đoạn dịch vụ.</p>
                <button type="button" class="btn btn-danger btn-sm w-100 fw-bold" onclick="switchTab('tab-overdue')">
                    <i class="bi bi-qr-code-scan me-1"></i> Quét Mã Trả Tiền Ngay
                </button>
            @else
                <div class="text-muted small fw-semibold">CẦN THANH TOÁN KỲ NÀY</div>
                <h3 class="fw-bold text-dark mt-1 mb-1">{{ number_format($pendingInvoices->sum('remaining_amount')) }}đ</h3>
                <div class="small text-muted mb-3">
                    {{ $pendingInvoices->isNotEmpty() ? $pendingInvoices->count() . ' hóa đơn đang chờ nộp' : 'Không có hóa đơn nợ đọng' }}
                </div>
                @if($pendingInvoices->isNotEmpty())
                    <button type="button" class="btn btn-warning btn-sm text-dark fw-bold w-100" onclick="switchTab('tab-pending')">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Xem Hóa Đơn & Quét QR
                    </button>
                @else
                    <div class="badge bg-success-subtle text-success py-2 w-100">
                        <i class="bi bi-check-circle-fill me-1"></i> Đã hoàn thành tiền nhà
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- TÁC VỤ NHANH DÀNH CHO KHÁCH THUÊ -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <a href="#tab-pending" onclick="switchTab('tab-pending')" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-primary text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-white text-primary rounded-3 fs-3">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">1. Quét Mã VietQR</h6>
                    <small class="text-white-50">Thanh toán app ngân hàng</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <button type="button" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-warning text-dark text-start w-100" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-dark text-warning rounded-3 fs-3">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">2. Báo Hỏng Thiết Bị</h6>
                    <small class="text-dark-50">Yêu cầu thợ sửa bóng đèn, vòi nước</small>
                </div>
            </div>
        </button>
    </div>
    <div class="col-12 col-md-4">
        <a href="#tab-feedback" onclick="switchTab('tab-feedback')" class="card text-decoration-none border-0 shadow-sm p-3 h-100 bg-info text-dark">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-dark text-info rounded-3 fs-3">
                    <i class="bi bi-chat-left-dots-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">3. Khiếu Nại / Góp Ý</h6>
                    <small class="text-dark-50">Thắc mắc số điện nước & dịch vụ</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- TABS ĐIỀU HƯỚNG CỦA KHÁCH THUÊ -->
<ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-3 border shadow-sm" id="tenantTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active position-relative" data-bs-toggle="pill" data-bs-target="#tab-pending">
            <i class="bi bi-clock-history me-1"></i> Đang Chờ Nộp ({{ $pendingInvoices->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link position-relative {{ $overdueInvoices->isNotEmpty() ? 'text-danger fw-bold' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-overdue">
            <i class="bi bi-exclamation-octagon-fill me-1"></i> Quá Hạn Nộp 
            @if($overdueInvoices->isNotEmpty())
                <span class="badge bg-danger ms-1">{{ $overdueInvoices->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-paid">
            <i class="bi bi-check2-circle me-1"></i> Đã Thanh Toán ({{ $paidInvoices->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-maintenance">
            <i class="bi bi-tools me-1"></i> Báo Hỏng Thiết Bị ({{ $maintenanceRequests->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-feedback">
            <i class="bi bi-chat-left-dots-fill me-1"></i> Ý Kiến / Khiếu Nại ({{ $myFeedbacks->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-fees">
            <i class="bi bi-tags-fill text-warning me-1"></i> Biểu Phí & Dịch Vụ
        </button>
    </li>
</ul>

<!-- NỘI DUNG CÁC TABS -->
<div class="tab-content" id="tenantTabContent">

    <!-- ==================== TAB 1: ĐANG CHỜ THANH TOÁN ==================== -->
    <div class="tab-pane fade show active" id="tab-pending">
        @forelse($pendingInvoices as $inv)
            <div class="card mb-4 shadow-sm border-warning">
                <div class="card-header bg-warning-subtle py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark font-monospace fs-6">Mã: {{ $inv->invoice_code }}</span>
                        <span class="fw-bold text-dark fs-6">Hóa Đơn Tháng {{ $inv->month }}/{{ $inv->year }}</span>
                    </div>
                    <span class="badge bg-warning text-dark fw-bold">Hạn nộp: {{ $inv->due_date->format('d/m/Y') }}</span>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-md-7">
                            <table class="table table-sm align-middle small mb-3">
                                <tbody>
                                    <tr>
                                        <td>Tiền phòng:</td>
                                        <td class="text-end fw-bold">{{ number_format($inv->room_price) }}đ</td>
                                    </tr>
                                    <tr>
                                        <td>Tiền điện ({{ $inv->electricity_old }} → {{ $inv->electricity_new }} = <b>{{ $inv->electricity_usage }} kWh</b>):</td>
                                        <td class="text-end fw-bold text-warning-emphasis">{{ number_format($inv->electricity_total) }}đ</td>
                                    </tr>
                                    <tr>
                                        <td>Tiền nước ({{ $inv->water_usage }} số):</td>
                                        <td class="text-end fw-bold text-info-emphasis">{{ number_format($inv->water_total) }}đ</td>
                                    </tr>
                                    @if(!empty($inv->fees_detail))
                                        @foreach($inv->fees_detail as $f)
                                            <tr>
                                                <td>{{ $f['name'] }}:</td>
                                                <td class="text-end fw-bold">{{ number_format($f['amount']) }}đ</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr class="table-light fs-5">
                                        <td class="fw-bold text-uppercase">CẦN THANH TOÁN:</td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($inv->remaining_amount) }}đ</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('portal.show', $inv->invoice_code) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> Xem chi tiết bảng kê
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="openFeedbackModal('{{ $inv->id }}', '{{ $inv->invoice_code }}')">
                                    <i class="bi bi-chat-square-dots"></i> Khiếu Nại / Ý Kiến Hóa Đơn
                                </button>
                            </div>
                        </div>

                        <!-- VIETQR THANH TOÁN -->
                        <div class="col-12 col-md-5 text-center border-start">
                            @if($inv->viet_qr_url)
                                <div class="fw-bold small text-muted mb-2">QUÉT MÃ NÀY BẰNG APP NGÂN HÀNG:</div>
                                <img src="{{ $inv->viet_qr_url }}" alt="VietQR" class="img-fluid rounded border shadow-sm p-1" style="max-height: 200px;">
                                <div class="small text-muted mt-2">
                                    STK: <b>{{ $room->property->bank_account_number }}</b> ({{ $room->property->bank_name }})
                                    <br>Chủ TK: <b>{{ $room->property->bank_account_holder }}</b>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-5 text-center shadow-sm">
                <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                <h5>Không có hóa đơn nào đang chờ thanh toán!</h5>
                <p class="text-muted mb-0">Bạn đã hoàn thành tiền phòng đầy đủ.</p>
            </div>
        @endforelse
    </div>

    <!-- ==================== TAB 2: QUÁ HẠN NỘP ==================== -->
    <div class="tab-pane fade" id="tab-overdue">
        @forelse($overdueInvoices as $inv)
            <div class="card mb-4 shadow-sm border-2 border-danger">
                <div class="card-header bg-danger text-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <span class="badge bg-white text-danger font-monospace fs-6">Mã: {{ $inv->invoice_code }}</span>
                        <span class="fw-bold fs-6">Hóa Đơn Tháng {{ $inv->month }}/{{ $inv->year }} (QUÁ HẠN)</span>
                    </div>
                    <span class="badge bg-white text-danger fw-bold">
                        Đã trễ {{ (int) now()->diffInDays($inv->due_date) }} ngày
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-danger mb-3 small d-flex align-items-center">
                        <i class="bi bi-clock-history fs-4 me-2"></i>
                        <div>
                            Hạn chót nộp tiền là ngày <b>{{ $inv->due_date->format('d/m/Y') }}</b>. Vui lòng thanh toán sớm để tránh bị tính phí phạt hoặc gián đoạn dịch vụ điện nước.
                        </div>
                    </div>

                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-md-7">
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between py-1 border-bottom small">
                                    <span>Tiền phòng:</span>
                                    <b>{{ number_format($inv->room_price) }}đ</b>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom small">
                                    <span>Tiền điện ({{ $inv->electricity_usage }} kWh):</span>
                                    <b>{{ number_format($inv->electricity_total) }}đ</b>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom small">
                                    <span>Tiền nước:</span>
                                    <b>{{ number_format($inv->water_total) }}đ</b>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom small">
                                    <span>Phí dịch vụ:</span>
                                    <b>{{ number_format($inv->other_fees) }}đ</b>
                                </div>
                                <div class="d-flex justify-content-between pt-2 fs-5 text-danger fw-bold">
                                    <span>TỔNG TIỀN NỢ:</span>
                                    <span>{{ number_format($inv->remaining_amount) }}đ</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('portal.show', $inv->invoice_code) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="openFeedbackModal('{{ $inv->id }}', '{{ $inv->invoice_code }}')">
                                    <i class="bi bi-chat-square-dots"></i> Khiếu Nại / Ý Kiến
                                </button>
                            </div>
                        </div>

                        <!-- VIETQR THANH TOÁN -->
                        <div class="col-12 col-md-5 text-center border-start">
                            @if($inv->viet_qr_url)
                                <div class="fw-bold small text-danger mb-2">QUÉT MÃ CHUYỂN KHOẢN NGAY:</div>
                                <img src="{{ $inv->viet_qr_url }}" alt="VietQR" class="img-fluid rounded border shadow-sm p-1" style="max-height: 200px;">
                                <div class="small text-muted mt-2">
                                    STK: <b>{{ $room->property->bank_account_number }}</b> ({{ $room->property->bank_name }})
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-5 text-center shadow-sm">
                <i class="bi bi-shield-check text-success fs-1 mb-2"></i>
                <h5>Không có hóa đơn nào quá hạn!</h5>
                <p class="text-muted mb-0">Bạn luôn thanh toán tiền phòng rất đúng hẹn.</p>
            </div>
        @endforelse
    </div>

    <!-- ==================== TAB 3: ĐÃ THANH TOÁN ==================== -->
    <div class="tab-pane fade" id="tab-paid">
        @forelse($paidInvoices as $inv)
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark font-monospace">Mã: {{ $inv->invoice_code }}</span>
                            <h6 class="fw-bold mb-0 text-dark">Hóa Đơn Tháng {{ $inv->month }}/{{ $inv->year }}</h6>
                        </div>
                        <span class="badge bg-success-subtle text-success fw-bold">
                            <i class="bi bi-check2"></i> Đã Thanh Toán
                        </span>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center text-muted small pt-2 border-top">
                        <div>
                            Tiền phòng: <b>{{ number_format($inv->room_price) }}đ</b> |
                            Điện: <b>{{ $inv->electricity_usage }} kWh</b> |
                            Nước: <b>{{ $inv->water_usage }} số</b>
                        </div>
                        <div class="fw-bold text-dark fs-6">
                            Tổng: {{ number_format($inv->total_amount) }}đ
                            <a href="{{ route('portal.show', $inv->invoice_code) }}" class="btn btn-sm btn-outline-primary ms-2 py-0 px-2">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-4 text-center text-muted shadow-sm">Chưa có lịch sử hóa đơn đã thanh toán.</div>
        @endforelse
    </div>

    <!-- ==================== TAB 4: BÁO HỎNG THIẾT BỊ ==================== -->
    <div class="tab-pane fade" id="tab-maintenance">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold text-dark"><i class="bi bi-tools text-primary me-2"></i>Danh Sách Báo Hỏng Thiết Bị Phòng {{ $room->room_number ?? '' }}</span>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                    <i class="bi bi-plus-lg me-1"></i> Báo Hỏng Mới
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Ngày Báo</th>
                            <th>Vấn Đề Báo Hỏng</th>
                            <th>Mô Tả Hiện Trạng</th>
                            <th>Trạng Thái Sửa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceRequests as $req)
                            <tr>
                                <td>{{ $req->reported_date->format('d/m/Y') }}</td>
                                <td class="fw-bold text-dark">{{ $req->title }}</td>
                                <td class="text-muted">{{ $req->description }}</td>
                                <td>
                                    <span class="badge bg-{{ $req->status_badge }}-subtle text-{{ $req->status_badge }} fw-bold">
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Phòng bạn không có hỏng hóc nào được ghi nhận.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 5: LỊCH SỬ KHIẾU NẠI & Ý KIẾN ==================== -->
    <div class="tab-pane fade" id="tab-feedback">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Ý Kiến / Khiếu Nại Hóa Đơn Của Bạn</span>
            </div>
            <div class="card-body p-0">
                @forelse($myFeedbacks as $fb)
                    <div class="p-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-dark font-monospace">Hóa đơn: {{ $fb->invoice->invoice_code }}</span>
                                <span class="badge bg-info-subtle text-info ms-1">{{ $fb->feedback_type_name }}</span>
                                <span class="text-muted small ms-2">{{ $fb->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="badge bg-{{ $fb->status_badge }}-subtle text-{{ $fb->status_badge }} fw-bold">
                                {{ $fb->status_label }}
                            </span>
                        </div>

                        <div class="p-2 bg-light rounded small text-dark mb-2">
                            <b>Nội dung bạn gửi:</b> "{{ $fb->content }}"
                        </div>

                        @if($fb->admin_reply)
                            <div class="p-2 bg-success-subtle rounded small text-success-emphasis">
                                <i class="bi bi-reply-fill me-1"></i><b>Chủ nhà phản hồi:</b> "{{ $fb->admin_reply }}"
                            </div>
                        @else
                            <div class="small text-muted fst-italic">
                                <i class="bi bi-hourglass-split"></i> Đang chờ Ban quản lý kiểm tra và phản hồi...
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Bạn chưa gửi khiếu nại hay thắc mắc nào về hóa đơn.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==================== TAB 6: BIỂU PHÍ & DỊCH VỤ PHÒNG ==================== -->
    <div class="tab-pane fade" id="tab-fees">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark fs-5">
                    <i class="bi bi-tags-fill text-primary me-2"></i>Chi Tiết Biểu Phí Dịch Vụ & Đơn Giá Căn Hộ Phòng {{ $room->room_number ?? '' }}
                </span>
                <span class="badge bg-primary fs-6">{{ $room->fees->count() ?? 0 }} khoản phí</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="p-3 bg-light rounded border h-100">
                            <div class="text-muted small">Tiền thuê phòng:</div>
                            <div class="h4 fw-bold text-primary mb-1">{{ number_format($tenant->currentContract->rental_price ?? $room->price ?? 0) }}đ<small class="fs-6 text-muted fw-normal">/tháng</small></div>
                            <div class="small text-muted">Kỳ hạn nộp theo hợp đồng</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 bg-light rounded border h-100">
                            <div class="text-muted small">⚡ Đơn giá Điện:</div>
                            <div class="h4 fw-bold text-dark mb-1">{{ number_format($room->electricity_rate ?? 0) }}đ <small class="fs-6 text-muted fw-normal">/kWh</small></div>
                            <div class="small text-muted">Tính theo công tơ điện riêng của phòng</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 bg-light rounded border h-100">
                            <div class="text-muted small">💧 Đơn giá Nước:</div>
                            <div class="h4 fw-bold text-dark mb-1">{{ number_format($room->water_rate ?? 0) }}đ <small class="fs-6 text-muted fw-normal">/{{ $room->water_calculation_type === 'per_person' ? 'người' : 'm³' }}</small></div>
                            <div class="small text-muted">{{ $room->water_calculation_type === 'per_person' ? 'Tính theo số người ở trong phòng' : 'Tính theo đồng hồ đo nước riêng' }}</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check text-success me-2"></i>Các Khoản Phí Dịch Vụ Đi Kèm Hàng Tháng</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tên Khoản Phí Dịch Vụ</th>
                                <th>Phương Thức Tính</th>
                                <th class="text-end" style="width: 160px;">Đơn Giá</th>
                                <th class="text-center" style="width: 100px;">Số Lượng</th>
                                <th class="text-end" style="width: 180px;">Thành Tiền Dự Kiến</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($room->fees ?? [] as $index => $fee)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $fee->fee_name }}</td>
                                    <td>
                                        @if($fee->fee_type === 'fixed')
                                            <span class="badge bg-secondary-subtle text-secondary">Cố định theo phòng</span>
                                        @elseif($fee->fee_type === 'per_person')
                                            <span class="badge bg-info-subtle text-info">Tính theo đầu người</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-dark">Tính theo số lượng xe/thiết bị</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace">{{ number_format($fee->unit_price, 0, ',', '.') }}đ</td>
                                    <td class="text-center">{{ $fee->quantity }}</td>
                                    <td class="text-end fw-bold text-primary font-monospace">{{ number_format($fee->calculateTotal(1), 0, ',', '.') }}đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Phòng không có khoản phí dịch vụ riêng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($room) && $room->assets->isNotEmpty())
                    <h6 class="fw-bold text-dark mt-4 mb-3"><i class="bi bi-box-seam text-info me-2"></i>Trang Thiết Bị & Nội Thất Bàn Giao Trong Phòng</h6>
                    <div class="row g-2">
                        @foreach($room->assets as $asset)
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="p-2 border rounded bg-light small d-flex justify-content-between align-items-center">
                                    <span><b>{{ $asset->asset_name }}</b> (SL: {{ $asset->quantity }})</span>
                                    <span class="badge bg-{{ $asset->condition === 'good' ? 'success' : 'warning' }}-subtle text-{{ $asset->condition === 'good' ? 'success' : 'dark' }}">
                                        {{ $asset->condition === 'good' ? 'Tốt' : 'Cần bảo trì' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- MODAL GỬI KHIẾU NẠI / Ý KIẾN VỀ HÓA ĐƠN -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="feedbackForm" action="" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-chat-square-dots me-2"></i>Khiếu Nại / Ý Kiến Về Hóa Đơn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border small mb-3">
                        Đang khiếu nại hóa đơn: <b id="modalInvoiceCodeDisplay" class="text-danger"></b>
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

<!-- MODAL BÁO HỎNG ĐỒ ĐẠC -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('portal.maintenance.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-tools me-2"></i>Báo Hỏng / Yêu Cầu Sửa Chữa Thiết Bị</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Thiết bị hoặc sự cố: <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="vd: Hỏng bóng đèn tuýp, vòi sen bị rỉ nước, điều hòa không mát...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả cụ thể hiện trạng: <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Mô tả chi tiết để chủ nhà chuẩn bị linh kiện thợ sang thay thế..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-send-fill me-1"></i> Gửi Yêu Cầu Sửa Chữa
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
        document.getElementById('feedbackForm').action = '/khach-thue/feedback/' + invoiceId;
        document.getElementById('modalInvoiceCodeDisplay').textContent = invoiceCode;
        var modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        modal.show();
    }
</script>
@endpush
