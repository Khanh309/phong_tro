@extends('layouts.app')

@section('title', 'Quản lý Hợp Đồng & Tiền Cọc')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Hợp Đồng Thuê & Quản Lý Cọc</h3>
        <p class="text-muted mb-0">Theo dõi thời hạn thuê, tiền cọc phòng và quy trình thanh lý trả phòng</p>
    </div>
    <a href="{{ route('contracts.create') }}" class="btn btn-primary">
        <i class="bi bi-file-earmark-plus me-1"></i> Ký Hợp Đồng Mới
    </a>
</div>

<!-- BỘ LỌC -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('contracts.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <select name="property_id" class="form-select" onchange="this.form.submit()">
                <option value="">🏢 Tất cả các Nhà trọ</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ $propertyId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>🟢 Đang có hiệu lực</option>
                <option value="expiring_soon" {{ $status == 'expiring_soon' ? 'selected' : '' }}>🟡 Sắp hết hạn</option>
                <option value="terminated" {{ $status == 'terminated' ? 'selected' : '' }}>⚪ Đã thanh lý / Kết thúc</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Lọc</button>
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
    </form>
</div>

<!-- BẢNG HỢP ĐỒNG -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Mã Hợp Đồng</th>
                    <th>Phòng & Cơ Sở</th>
                    <th>Khách Thuê Đại Diện</th>
                    <th>Giá Thuê</th>
                    <th>Tiền Cọc</th>
                    <th>Thời Hạn Thuê</th>
                    <th>Trạng Thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>
                            <a href="{{ route('contracts.show', $contract->id) }}" class="fw-bold text-primary text-decoration-none">
                                <code>{{ $contract->contract_code }}</code>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('rooms.show', $contract->room->id) }}" class="fw-bold text-dark text-decoration-none">
                                Phòng {{ $contract->room->room_number }}
                            </a>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $contract->room->property->name }}</div>
                        </td>
                        <td>
                            <a href="{{ route('tenants.show', $contract->tenant->id) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $contract->tenant->name }}
                            </a>
                            <div class="text-muted small">{{ $contract->tenant->phone }}</div>
                        </td>
                        <td class="fw-bold">{{ number_format($contract->rental_price, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ number_format($contract->deposit_amount, 0, ',', '.') }}đ</span>
                            <div>
                                <span class="badge bg-light text-muted" style="font-size: 0.7rem;">{{ $contract->deposit_status_label }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                {{ $contract->start_date->format('d/m/Y') }} → {{ $contract->end_date->format('d/m/Y') }}
                            </div>
                            @if($contract->status === 'active' && now()->gt($contract->end_date->subDays(30)))
                                <span class="badge bg-warning-subtle text-dark" style="font-size: 0.7rem;">Sắp hết hạn</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $contract->status_badge }}-subtle text-{{ $contract->status_badge }}">
                                {{ $contract->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-primary" title="Xem hợp đồng">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('contracts.print', $contract->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="In hợp đồng mẫu">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if($contract->status !== 'terminated')
                                    <a href="{{ route('contracts.checkout', $contract->id) }}" class="btn btn-sm btn-outline-danger" title="Trả phòng & Trả cọc">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy hợp đồng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contracts->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $contracts->links() }}
        </div>
    @endif
</div>
@endsection
