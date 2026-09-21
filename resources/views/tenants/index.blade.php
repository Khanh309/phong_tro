@extends('layouts.app')

@section('title', 'Quản lý Khách Thuê')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Hồ Sơ Khách Thuê Trọ</h3>
        <p class="text-muted mb-0">Quản lý CCCD, thông tin liên hệ, xe cộ và tình trạng khai báo tạm trú</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tenants.police_report') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Xuất DS Khai Báo Tạm Trú
        </a>
        <a href="{{ route('tenants.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Thêm Khách Thuê Mới
        </a>
    </div>
</div>

<!-- TÌM KIẾM & BỘ LỌC -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('tenants.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, SĐT, CCCD, biển số xe..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="residence" class="form-select" onchange="this.form.submit()">
                <option value="">-- Tình trạng Tạm trú --</option>
                <option value="registered" {{ $residence == 'registered' ? 'selected' : '' }}>Đã đăng ký tạm trú</option>
                <option value="not_registered" {{ $residence == 'not_registered' ? 'selected' : '' }}>Chưa đăng ký tạm trú</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Tìm kiếm</button>
            <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
    </form>
</div>

<!-- BẢNG DANH SÁCH KHÁCH THUÊ -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Họ và Tên</th>
                    <th>Số Điện Thoại</th>
                    <th>CCCD / CMND</th>
                    <th>Phòng Đang Ở</th>
                    <th>Quê Quán</th>
                    <th>Biển Số Xe</th>
                    <th>Tạm Trú</th>
                    <th>Tài Khoản App</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td>
                            <a href="{{ route('tenants.show', $tenant->id) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $tenant->name }}
                            </a>
                            @if($tenant->gender)
                                <span class="badge bg-light text-muted ms-1">{{ $tenant->gender }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="tel:{{ $tenant->phone }}" class="text-dark text-decoration-none">
                                <i class="bi bi-telephone text-success me-1"></i> {{ $tenant->phone }}
                            </a>
                        </td>
                        <td>
                            <code>{{ $tenant->id_card_number ?: '---' }}</code>
                        </td>
                        <td>
                            @if($tenant->currentContract && $tenant->currentContract->room)
                                <a href="{{ route('rooms.show', $tenant->currentContract->room->id) }}" class="badge bg-primary-subtle text-primary text-decoration-none">
                                    P.{{ $tenant->currentContract->room->room_number }} ({{ $tenant->currentContract->room->property->name }})
                                </a>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Đã trả phòng</span>
                            @endif
                        </td>
                        <td>{{ $tenant->hometown ?: '---' }}</td>
                        <td>
                            @if($tenant->vehicle_plate)
                                <span class="badge bg-dark-subtle text-dark">{{ $tenant->vehicle_plate }}</span>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $tenant->residence_badge }}-subtle text-{{ $tenant->residence_badge }}">
                                {{ $tenant->residence_label }}
                            </span>
                        </td>
                        <td>
                            @if($tenant->user)
                                <span class="badge bg-success-subtle text-success" title="{{ $tenant->user->email }}">
                                    <i class="bi bi-person-check-fill me-1"></i>Có TK
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">
                                    <i class="bi bi-person-x me-1"></i>Chưa có
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('tenants.show', $tenant->id) }}" class="btn btn-sm btn-outline-primary" title="Xem hồ sơ & Quản lý TK">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('tenants.edit', $tenant->id) }}" class="btn btn-sm btn-outline-secondary" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Không tìm thấy khách thuê nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $tenants->links() }}
        </div>
    @endif
</div>
@endsection
