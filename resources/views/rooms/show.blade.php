@extends('layouts.app')

@section('title', 'Chi Tiết Phòng ' . $room->room_number . ' - ' . $room->property->name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0">Phòng {{ $room->room_number }}</h3>
            <span class="badge bg-{{ $room->status_badge }}-subtle text-{{ $room->status_badge }} fs-6">
                {{ $room->status_label }}
            </span>
            <span class="badge bg-secondary-subtle text-secondary">Tầng {{ $room->floor }}</span>
        </div>
        <p class="text-muted mb-0">Thuộc: <b>{{ $room->property->name }}</b> ({{ $room->property->address }})</p>
    </div>
    <div class="d-flex gap-2">
        @if($room->status === 'available')
            <a href="{{ route('contracts.create', ['room_id' => $room->id]) }}" class="btn btn-success">
                <i class="bi bi-person-plus-fill me-1"></i> Ký Hợp Đồng Cho Thuê
            </a>
        @elseif($room->status === 'occupied' && $room->currentContract)
            <a href="{{ route('invoices.create', ['room_id' => $room->id]) }}" class="btn btn-warning text-dark fw-bold">
                <i class="bi bi-receipt me-1"></i> Lập Hóa Đơn Tháng
            </a>
            <a href="{{ route('contracts.checkout', $room->currentContract->id) }}" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right me-1"></i> Trả Phòng / Quyết Toán Cọc
            </a>
        @endif
        <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-outline-dark">
            <i class="bi bi-pencil me-1"></i> Sửa Phòng
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: THÔNG SỐ PHÒNG & KHÁCH THUÊ HIỆN TẠI -->
    <div class="col-12 col-lg-4">
        <!-- Ảnh phòng trọ -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-images text-primary me-2"></i>Ảnh Phòng ({{ count($room->images ?? []) }})</span>
                <a href="{{ route('rooms.edit', $room->id) }}" class="small text-decoration-none">
                    <i class="bi bi-pencil-square me-1"></i>Quản lý ảnh
                </a>
            </div>
            <div class="card-body p-2">
                @if(!empty($room->images) && count($room->images) > 0)
                    <div class="row g-2">
                        @foreach($room->images as $img)
                            <div class="col-6">
                                <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $img) }}" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="height: 110px;" alt="Ảnh phòng">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3 text-muted small">
                        <i class="bi bi-camera fs-3 d-block mb-1"></i>
                        Chưa có ảnh thực tế tải lên.<br>
                        <a href="{{ route('rooms.edit', $room->id) }}">Bấm vào đây để tải ảnh phòng</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Thông số cơ bản -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Thông Số Phòng</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Giá thuê cơ bản:</span>
                    <span class="fw-bold text-primary fs-5">{{ number_format($room->price, 0, ',', '.') }}đ/tháng</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Diện tích phòng:</span>
                    <span class="fw-bold">{{ $room->area }} m²</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Sức chứa tối đa:</span>
                    <span class="fw-bold">{{ $room->max_tenants }} người</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Mã công tơ điện:</span>
                    <code>{{ $room->electricity_meter_number ?: 'Chưa đặt mã' }}</code>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Đơn giá điện:</span>
                    <span class="fw-bold text-warning-emphasis">{{ number_format($room->electricity_rate, 0, ',', '.') }}đ / kWh</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Cách tính tiền nước:</span>
                    <span class="badge bg-info-subtle text-info">{{ $room->water_type_label }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Đơn giá nước:</span>
                    <span class="fw-bold text-info-emphasis">{{ number_format($room->water_rate, 0, ',', '.') }}đ</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Cách tính tiền mạng:</span>
                    <span class="badge bg-primary-subtle text-primary">{{ $room->internet_type_label }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Đơn giá mạng:</span>
                    <span class="fw-bold text-primary">{{ number_format($room->internet_rate ?? 100000, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>

        <!-- Khách thuê hiện tại -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-person-badge text-primary me-2"></i>Khách Đang Thuê</span>
                @if($room->currentContract)
                    <a href="{{ route('contracts.show', $room->currentContract->id) }}" class="small text-decoration-none">Hợp đồng #{{ $room->currentContract->contract_code }}</a>
                @endif
            </div>
            <div class="card-body">
                @if($room->currentContract && $room->currentContract->tenant)
                    @php $t = $room->currentContract->tenant; @endphp
                    <h5 class="fw-bold mb-1">{{ $t->name }}</h5>
                    <div class="text-muted small mb-2"><i class="bi bi-telephone-fill text-success me-1"></i> {{ $t->phone }}</div>
                    <div class="small mb-1">CCCD: <code>{{ $t->id_card_number ?: '---' }}</code></div>
                    <div class="small mb-1">Quê quán: <b>{{ $t->hometown ?: '---' }}</b></div>
                    <div class="small mb-2">Biển số xe: <b>{{ $t->vehicle_plate ?: '---' }}</b></div>
                    <div class="small mb-3">
                        Tình trạng tạm trú: 
                        <span class="badge bg-{{ $t->residence_badge }}-subtle text-{{ $t->residence_badge }}">
                            {{ $t->residence_label }}
                        </span>
                    </div>

                    <div class="bg-light p-2 rounded small">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tiền cọc giữ:</span>
                            <span class="fw-bold text-dark">{{ number_format($room->currentContract->deposit_amount, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted">Thời hạn thuê:</span>
                            <span>{{ $room->currentContract->start_date->format('d/m/Y') }} → {{ $room->currentContract->end_date->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    @if($room->currentContract->members->isNotEmpty())
                        <div class="mt-3 pt-2 border-top">
                            <div class="text-muted small fw-bold mb-1">Người ở cùng ({{ $room->currentContract->members->count() }}):</div>
                            <ul class="list-unstyled mb-0 small">
                                @foreach($room->currentContract->members as $mem)
                                    <li><i class="bi bi-person me-1"></i> {{ $mem->name }} ({{ $mem->relationship }}) - {{ $mem->phone }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-person-x fs-1 d-block mb-1"></i>
                        Phòng hiện chưa có khách thuê.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: PHÍ DỊCH VỤ RIÊNG & TÀI SẢN NỘI THẤT -->
    <div class="col-12 col-lg-8">
        <!-- 1. CẤU HÌNH CÁC KHOẢN PHÍ RIÊNG THEO PHÒNG -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold text-dark"><i class="bi bi-tags-fill text-primary me-2"></i>Các Khoản Phí Dịch Vụ Của Phòng</span>
                    <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $room->fees->count() }} khoản phí</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Khoản Phí Mới
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Tên Khoản Phí</th>
                            <th>Cách Tính</th>
                            <th>Đơn Giá</th>
                            <th>Số Lượng</th>
                            <th>Thành Tiền Dự Kiến</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($room->fees as $fee)
                            <tr>
                                <td class="fw-semibold">{{ $fee->fee_name }}</td>
                                <td>
                                    @if($fee->fee_type === 'fixed')
                                        <span class="badge bg-secondary-subtle text-secondary">Cố định/Phòng</span>
                                    @elseif($fee->fee_type === 'per_person')
                                        <span class="badge bg-info-subtle text-info">Theo đầu người</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark">Theo số lượng xe/thiết bị</span>
                                    @endif
                                </td>
                                <td>{{ number_format($fee->unit_price, 0, ',', '.') }}đ</td>
                                <td>{{ $fee->quantity }}</td>
                                <td class="fw-bold text-dark">{{ number_format($fee->calculateTotal(2), 0, ',', '.') }}đ</td>
                                <td>
                                    <form action="{{ route('rooms.fees.destroy', $fee->id) }}" method="POST" onsubmit="return confirm('Xóa khoản phí này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Chưa cấu hình khoản phí dịch vụ nào cho phòng này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. QUẢN LÝ TÀI SẢN / NỘI THẤT TRONG PHÒNG -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold text-dark"><i class="bi bi-box-seam-fill text-primary me-2"></i>Tài Sản & Trang Thiết Bị Phòng</span>
                    <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $room->assets->count() }} món đồ</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                    <i class="bi bi-plus-lg me-1"></i> Bổ Sung Thiết Bị
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Tên Thiết Bị / Đồ Đạc</th>
                            <th>Số Lượng</th>
                            <th>Tình Trạng</th>
                            <th>Giá Trị</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($room->assets as $asset)
                            <tr>
                                <td class="fw-semibold">{{ $asset->name }}</td>
                                <td>{{ $asset->quantity }}</td>
                                <td><span class="badge bg-success-subtle text-success">{{ $asset->condition }}</span></td>
                                <td>{{ $asset->price ? number_format($asset->price, 0, ',', '.') . 'đ' : '---' }}</td>
                                <td>
                                    <form action="{{ route('rooms.assets.destroy', $asset->id) }}" method="POST" onsubmit="return confirm('Xóa tài sản này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Chưa ghi nhận tài sản nào trong phòng này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. LỊCH SỬ HÓA ĐƠN GẦN NHẤT CỦA PHÒNG -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Lịch Sử Bảng Kê & Chi Tiết Phí Theo Tháng Của Phòng</span>
                <span class="badge bg-primary">{{ $room->invoices->count() }} kỳ hóa đơn</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Kỳ Tháng</th>
                            <th class="text-end">Tiền Phòng</th>
                            <th>⚡ Điện</th>
                            <th>💧 Nước</th>
                            <th>Phí Dịch Vụ (Wifi, Rác, Xe...)</th>
                            <th class="text-end">Tổng Tiền</th>
                            <th class="text-end">Đã Trả / Nợ</th>
                            <th class="text-center">Trạng Thái</th>
                            <th class="text-center">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($room->invoices as $inv)
                            <tr>
                                <td class="fw-bold text-nowrap">Tháng {{ $inv->month }}/{{ $inv->year }}</td>
                                <td class="text-end fw-semibold">{{ number_format($inv->room_price, 0, ',', '.') }}đ</td>
                                <td>
                                    <div class="fw-semibold text-warning-emphasis">{{ number_format($inv->electricity_total, 0, ',', '.') }}đ</div>
                                    <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $inv->electricity_usage }} kWh × {{ number_format($inv->electricity_rate) }}đ</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-info-emphasis">{{ number_format($inv->water_total, 0, ',', '.') }}đ</div>
                                    <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $inv->water_usage }} {{ $room->water_calculation_type === 'per_person' ? 'người' : 'm³' }}</div>
                                </td>
                                <td>
                                    @if(!empty($inv->fees_detail))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($inv->fees_detail as $fd)
                                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ $fd['name'] }}: {{ number_format($fd['amount']) }}đ</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">{{ number_format($inv->other_fees) }}đ</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-danger">{{ number_format($inv->total_amount, 0, ',', '.') }}đ</td>
                                <td class="text-end">
                                    <div class="text-success fw-semibold">{{ number_format($inv->paid_amount, 0, ',', '.') }}đ</div>
                                    @if($inv->remaining_amount > 0)
                                        <div class="text-danger fw-bold" style="font-size: 0.72rem;">Nợ: {{ number_format($inv->remaining_amount) }}đ</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $inv->status_badge }}-subtle text-{{ $inv->status_badge }}">
                                        {{ $inv->status_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Xem bảng kê chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Chưa có hóa đơn nào cho phòng này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL THÊM PHÍ DỊCH VỤ RIÊNG CHO PHÒNG -->
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rooms.fees.store', $room->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm Khoản Phí Dịch Vụ Cho Phòng {{ $room->room_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên khoản phí <span class="text-danger">*</span></label>
                        <input type="text" name="fee_name" class="form-control" required placeholder="vd: Tiền rác, Wifi, Gửi xe máy, Máy giặt...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cách tính phí <span class="text-danger">*</span></label>
                        <select name="fee_type" class="form-select" required>
                            <option value="fixed">Cố định theo phòng (vd: 100k/tháng)</option>
                            <option value="per_person">Tính theo số người ở (vd: 50k/người)</option>
                            <option value="per_unit">Tính theo số lượng thiết bị/xe (vd: 100k/xe máy)</option>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="unit_price" class="form-control" required min="0" step="1000" placeholder="100000">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Số lượng</label>
                            <input type="number" name="quantity" class="form-control" required min="1" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold">Thêm Khoản Phí</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BỔ SUNG TÀI SẢN NỘI THẤT -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rooms.assets.store', $room->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Bổ Sung Tài Sản Vào Phòng {{ $room->room_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên thiết bị / nội thất <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="vd: Điều hòa Daikin, Nóng lạnh 20L, Giường ngủ...">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Số lượng</label>
                            <input type="number" name="quantity" class="form-control" required min="1" value="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tình trạng</label>
                            <input type="text" name="condition" class="form-control" required value="Hoạt động tốt">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá trị ước tính (VNĐ)</label>
                        <input type="number" name="price" class="form-control" min="0" step="100000" placeholder="vd: 5000000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold">Lưu Tài Sản</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
