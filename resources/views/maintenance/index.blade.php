@extends('layouts.app')

@section('title', 'Báo Hỏng & Sửa Chữa')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-tools text-primary me-2"></i>Báo Hỏng & Yêu Cầu Sửa Chữa</h3>
        <p class="text-muted mb-0">Theo dõi sự cố điện nước, thiết bị phòng và chi phí sửa chữa bảo trì</p>
    </div>
    <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Ghi Nhận Sự Cố Mới
    </a>
</div>

<!-- BỘ LỌC -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('maintenance.index') }}" class="row g-2 align-items-center">
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
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>🔴 Chờ xử lý</option>
                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>🟡 Đang sửa chữa</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>🟢 Đã hoàn thành</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Lọc</button>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
    </form>
</div>

<!-- BẢNG SỰ CỐ -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Ngày Báo</th>
                    <th>Phòng & Tòa Nhà</th>
                    <th>Nội Dung Báo Hỏng</th>
                    <th>Chi Phí Sửa (VNĐ)</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Xong</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->reported_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('rooms.show', $req->room->id) }}" class="fw-bold text-dark text-decoration-none">
                                Phòng {{ $req->room->room_number }}
                            </a>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $req->room->property->name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $req->title }}</div>
                            <div class="text-muted small">{{ $req->description }}</div>
                        </td>
                        <td class="fw-bold text-dark">{{ number_format($req->cost, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="badge bg-{{ $req->status_badge }}-subtle text-{{ $req->status_badge }} fw-bold">
                                {{ $req->status_label }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $req->resolved_date ? $req->resolved_date->format('d/m/Y') : '---' }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('maintenance.edit', $req->id) }}" class="btn btn-sm btn-outline-secondary" title="Sửa / Cập nhật tiến độ">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('maintenance.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Xóa phiếu này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Không có yêu cầu báo hỏng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
