@extends('layouts.app')

@section('title', 'Chi Phí Nhà Trọ & Khoản Đóng Cho Nhà Nước')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-bank text-danger me-2"></i>Chi Phí Nhà Trọ & Đóng Cho Nhà Nước</h3>
        <p class="text-muted mb-0">Quản lý tách riêng dòng CHI: Tiền điện tổng EVN, Nước tổng, Thuế nộp Nhà nước, Rác & Bảo trì</p>
    </div>
    <a href="{{ route('expenses.create', ['property_id' => $propertyId, 'month' => $month, 'year' => $year]) }}" class="btn btn-danger">
        <i class="bi bi-plus-circle me-1"></i> Ghi Nhận Khoản Chi Mới
    </a>
</div>

<!-- BỘ LỌC CHI PHÍ -->
<div class="card p-3 mb-4 shadow-sm">
    <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <select name="property_id" class="form-select" onchange="this.form.submit()">
                <option value="">🏢 Tất cả các Nhà trọ</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ $propertyId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="month" class="form-select" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">-- Loại chi phí --</option>
                <option value="electricity_evn" {{ $type == 'electricity_evn' ? 'selected' : '' }}>⚡ Điện tổng EVN</option>
                <option value="water_supply" {{ $type == 'water_supply' ? 'selected' : '' }}>💧 Nước sạch tổng</option>
                <option value="state_tax" {{ $type == 'state_tax' ? 'selected' : '' }}>🏛️ Thuế nộp Nhà nước</option>
                <option value="internet_bill" {{ $type == 'internet_bill' ? 'selected' : '' }}>🌐 Cáp quang tổng</option>
                <option value="waste_collection" {{ $type == 'waste_collection' ? 'selected' : '' }}>🗑️ Rác môi trường</option>
                <option value="maintenance_repair" {{ $type == 'maintenance_repair' ? 'selected' : '' }}>🛠️ Sửa chữa bảo trì</option>
            </select>
        </div>
        <div class="col-6 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Lọc</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
        </div>
    </form>
</div>

<!-- THỐNG KÊ CHI TIẾT DÒNG CHI -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-start border-danger border-4">
            <div class="text-muted small">TỔNG CHI PHÍ THÁNG {{ $month }}/{{ $year }}</div>
            <h4 class="fw-bold text-danger mt-1 mb-0">{{ number_format($totalExpenses, 0, ',', '.') }}đ</h4>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-start border-warning border-4">
            <div class="text-muted small">TIỀN ĐIỆN TỔNG TRẢ EVN</div>
            <h4 class="fw-bold text-warning-emphasis mt-1 mb-0">{{ number_format($evnTotal, 0, ',', '.') }}đ</h4>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-start border-dark border-4">
            <div class="text-muted small">THUẾ NỘP NHÀ NƯỚC</div>
            <h4 class="fw-bold text-dark mt-1 mb-0">{{ number_format($taxTotal, 0, ',', '.') }}đ</h4>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-start border-info border-4">
            <div class="text-muted small">NƯỚC TỔNG & VẬN HÀNH</div>
            <h4 class="fw-bold text-info-emphasis mt-1 mb-0">{{ number_format($waterTotal + $operationTotal, 0, ',', '.') }}đ</h4>
        </div>
    </div>
</div>

<!-- BẢNG DANH SÁCH CHI PHÍ -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Ngày Chi</th>
                    <th>Nhà Trọ / Cơ Sở</th>
                    <th>Loại Chi Phí</th>
                    <th>Khoản Mục Chi</th>
                    <th>Chỉ Số Tiêu Thụ Tổng</th>
                    <th>Số Tiền Chi (VNĐ)</th>
                    <th>Người Chi</th>
                    <th>Ghi Chú</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->payment_date->format('d/m/Y') }}</td>
                        <td class="fw-bold text-dark">{{ $expense->property->name }}</td>
                        <td>
                            <span class="badge bg-{{ $expense->expense_type_badge }}-subtle text-{{ $expense->expense_type_badge }} fw-bold">
                                {{ $expense->expense_type_name }}
                            </span>
                        </td>
                        <td class="fw-semibold">{{ $expense->title }}</td>
                        <td>
                            @if($expense->total_meter_usage)
                                <span class="badge bg-light text-dark">
                                    {{ number_format($expense->total_meter_usage, 1) }} {{ $expense->expense_type === 'electricity_evn' ? 'kWh' : 'm³' }}
                                </span>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>
                        <td class="fw-bold text-danger fs-6">{{ number_format($expense->amount, 0, ',', '.') }}đ</td>
                        <td>{{ $expense->paid_by ?: 'Chủ nhà' }}</td>
                        <td class="small text-muted" style="max-width: 200px;">{{ $expense->notes }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-secondary" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Xóa khoản chi này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-cash-stack text-muted fs-1 d-block mb-2"></i>
                            Chưa ghi nhận khoản chi phí nào trong Tháng {{ $month }}/{{ $year }}.
                            <div class="mt-2">
                                <a href="{{ route('expenses.create', ['property_id' => $propertyId, 'month' => $month, 'year' => $year]) }}" class="btn btn-danger btn-sm">
                                    Ghi nhận chi phí ngay
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
