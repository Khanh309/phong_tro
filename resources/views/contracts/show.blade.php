@extends('layouts.app')

@section('title', 'Hợp Đồng #' . $contract->contract_code)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0">Hợp Đồng #{{ $contract->contract_code }}</h3>
            <span class="badge bg-{{ $contract->status_badge }}-subtle text-{{ $contract->status_badge }} fs-6">
                {{ $contract->status_label }}
            </span>
        </div>
        <p class="text-muted mb-0">Phòng {{ $contract->room->room_number }} - <b>{{ $contract->room->property->name }}</b></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.print', $contract->id) }}" target="_blank" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> In Hợp Đồng Chuẩn
        </a>
        @if($contract->status !== 'terminated')
            <a href="{{ route('contracts.checkout', $contract->id) }}" class="btn btn-danger">
                <i class="bi bi-box-arrow-right me-1"></i> Thanh Lý / Trả Phòng & Cọc
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: THÔNG TIN HỢP ĐỒNG & KHÁCH KÝ -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Chi Tiết Hợp Đồng</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Giá thuê hàng tháng:</span>
                    <span class="fw-bold text-primary fs-5">{{ number_format($contract->rental_price, 0, ',', '.') }}đ</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Tiền đặt cọc:</span>
                    <div>
                        <span class="fw-bold text-dark">{{ number_format($contract->deposit_amount, 0, ',', '.') }}đ</span>
                        <span class="badge bg-light text-muted ms-1">({{ $contract->deposit_status_label }})</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Ngày bắt đầu:</span>
                    <span class="fw-semibold">{{ $contract->start_date->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Ngày kết thúc:</span>
                    <span class="fw-semibold text-danger">{{ $contract->end_date->format('d/m/Y') }}</span>
                </div>
                @if($contract->terminated_at)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Ngày thanh lý:</span>
                        <span class="text-muted">{{ $contract->terminated_at->format('d/m/Y') }}</span>
                    </div>
                @endif
                <div class="py-2">
                    <span class="text-muted d-block mb-1">Điều khoản & Quy định:</span>
                    <div class="small bg-light p-3 rounded" style="white-space: pre-line;">{{ $contract->terms ?: 'Theo quy định chung của nhà trọ.' }}</div>
                </div>
                @if($contract->checkout_note)
                    <div class="py-2">
                        <span class="text-muted d-block mb-1">Ghi chú quyết toán trả cọc:</span>
                        <div class="small bg-warning-subtle p-2 rounded text-dark">{{ $contract->checkout_note }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Khách đại diện -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold text-dark"><i class="bi bi-person-fill text-primary me-2"></i>Bên B: Người Đại Diện Thuê</span>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-1"><a href="{{ route('tenants.show', $contract->tenant->id) }}" class="text-decoration-none text-dark">{{ $contract->tenant->name }}</a></h5>
                <div class="text-muted small mb-2"><i class="bi bi-telephone text-success"></i> {{ $contract->tenant->phone }}</div>
                <div class="small mb-1">CCCD: <code>{{ $contract->tenant->id_card_number ?: '---' }}</code></div>
                <div class="small mb-1">Quê quán: <b>{{ $contract->tenant->hometown ?: '---' }}</b></div>
                <div class="small">Biển số xe: <b>{{ $contract->tenant->vehicle_plate ?: '---' }}</b></div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: THÀNH VIÊN Ở CÙNG & TÀI SẢN BÀN GIAO -->
    <div class="col-12 col-lg-7">
        <!-- 1. DANH SÁCH THÀNH VIÊN Ở CÙNG PHÒNG -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Người Ở Cùng Trong Phòng</span>
                    <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $contract->members->count() }} người</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus me-1"></i> Thêm Người Ở Cùng
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Họ và Tên</th>
                            <th>Số Điện Thoại</th>
                            <th>Số CCCD</th>
                            <th>Quan Hệ</th>
                            <th>Biển Số Xe</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->members as $mem)
                            <tr>
                                <td class="fw-bold">{{ $mem->name }}</td>
                                <td>{{ $mem->phone ?: '---' }}</td>
                                <td><code>{{ $mem->id_card_number ?: '---' }}</code></td>
                                <td><span class="badge bg-light text-dark">{{ $mem->relationship ?: 'Bạn' }}</span></td>
                                <td>{{ $mem->vehicle_plate ?: '---' }}</td>
                                <td>
                                    <form action="{{ route('contracts.members.destroy', $mem->id) }}" method="POST" onsubmit="return confirm('Xóa người này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Chưa đăng ký người ở cùng (Chỉ có khách đại diện).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. DANH MỤC NỘI THẤT BÀN GIAO CỦA PHÒNG -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Tài Sản Nội Thất Bàn Giao Kèm Phòng</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Tên Thiết Bị</th>
                            <th>Số Lượng</th>
                            <th>Tình Trạng Lúc Bàn Giao</th>
                            <th>Giá Trị Ước Tính</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->room->assets as $asset)
                            <tr>
                                <td class="fw-semibold">{{ $asset->name }}</td>
                                <td>{{ $asset->quantity }}</td>
                                <td><span class="badge bg-success-subtle text-success">{{ $asset->condition }}</span></td>
                                <td>{{ $asset->price ? number_format($asset->price, 0, ',', '.') . 'đ' : '---' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Chưa có danh mục tài sản bàn giao cho phòng này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL THÊM THÀNH VIÊN Ở CÙNG -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('contracts.members.store', $contract->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm Thành Viên Ở Cùng Phòng {{ $contract->room->room_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="vd: Trần Văn B">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" placeholder="vd: 0988...">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Số CCCD</label>
                            <input type="text" name="id_card_number" class="form-control" placeholder="vd: 001099...">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Quan hệ với chủ HĐ</label>
                            <input type="text" name="relationship" class="form-control" placeholder="vd: Em trai, Bạn học, Vợ/Chồng">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Biển số xe máy</label>
                            <input type="text" name="vehicle_plate" class="form-control" placeholder="vd: 29X1-123.45">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold">Lưu Thành Viên</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
