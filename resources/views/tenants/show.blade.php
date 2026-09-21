@extends('layouts.app')

@section('title', 'Hồ Sơ Khách ' . $tenant->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold text-dark mb-0">{{ $tenant->name }}</h3>
            <span class="badge bg-{{ $tenant->residence_badge }}-subtle text-{{ $tenant->residence_badge }} fs-6">
                {{ $tenant->residence_label }}
            </span>
        </div>
        <p class="text-muted mb-0"><i class="bi bi-telephone-fill text-success me-1"></i> {{ $tenant->phone }} | Quê: {{ $tenant->hometown ?: 'Chưa nhập' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-pencil me-1"></i> Chỉnh Sửa
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: THÔNG TIN CHI TIẾT -->
    <div class="col-12 col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Chi Tiết Pháp Lý & Liên Hệ</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Họ và tên:</span>
                    <span class="fw-bold">{{ $tenant->name }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Số điện thoại:</span>
                    <a href="tel:{{ $tenant->phone }}" class="fw-bold text-success text-decoration-none">{{ $tenant->phone }}</a>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Giới tính:</span>
                    <span>{{ $tenant->gender }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Ngày sinh:</span>
                    <span>{{ $tenant->dob ? $tenant->dob->format('d/m/Y') : '---' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Email:</span>
                    <span>{{ $tenant->email ?: '---' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Số CCCD:</span>
                    <code class="fs-6 fw-bold">{{ $tenant->id_card_number ?: '---' }}</code>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Ngày cấp & Nơi cấp:</span>
                    <span class="text-end">{{ $tenant->id_card_date ? $tenant->id_card_date->format('d/m/Y') : '' }} ({{ $tenant->id_card_place ?: '---' }})</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Quê quán thường trú:</span>
                    <span class="fw-semibold">{{ $tenant->hometown ?: '---' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Biển số xe:</span>
                    <span class="badge bg-dark-subtle text-dark">{{ $tenant->vehicle_plate ?: '---' }}</span>
                </div>
                <div class="py-2">
                    <span class="text-muted d-block mb-1">Ghi chú:</span>
                    <div class="small bg-light p-2 rounded">{{ $tenant->notes ?: 'Không có ghi chú.' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: LỊCH SỬ HỢP ĐỒNG THUÊ -->
    <div class="col-12 col-md-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Lịch Sử Thuê Trọ ({{ $tenant->contracts->count() }} Hợp Đồng)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Mã HĐ</th>
                                <th>Phòng & Tòa Nhà</th>
                                <th>Giá Thuê</th>
                                <th>Thời Hạn</th>
                                <th>Trạng Thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenant->contracts as $contract)
                                <tr>
                                    <td><code>{{ $contract->contract_code }}</code></td>
                                    <td>
                                        <a href="{{ route('rooms.show', $contract->room->id) }}" class="fw-bold text-primary text-decoration-none">
                                            Phòng {{ $contract->room->room_number }}
                                        </a>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $contract->room->property->name }}</div>
                                    </td>
                                    <td class="fw-bold">{{ number_format($contract->rental_price, 0, ',', '.') }}đ</td>
                                    <td>{{ $contract->start_date->format('d/m/Y') }} → {{ $contract->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $contract->status_badge }}-subtle text-{{ $contract->status_badge }}">
                                            {{ $contract->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            Xem HĐ
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Khách chưa có hợp đồng thuê nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
