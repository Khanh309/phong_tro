@extends('layouts.app')

@section('title', 'Bảng Kê Đăng Ký Tạm Trú Công An Phường')

@section('content')
<div class="d-print-none d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-danger me-2"></i>Danh Sách Khai Báo Tạm Trú Lưu Trú</h3>
        <p class="text-muted mb-0">Mẫu bảng kê danh sách người lưu trú tại cơ sở gửi Công an phường/xã</p>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('tenants.police_report') }}" class="d-flex gap-2">
            <select name="property_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Tất cả các nhà trọ --</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ $propertyId == $prop->id ? 'selected' : '' }}>{{ $prop->name }}</option>
                @endforeach
            </select>
        </form>
        <button onclick="window.print()" class="btn btn-dark btn-sm">
            <i class="bi bi-printer-fill me-1"></i> In danh sách này
        </button>
    </div>
</div>

<!-- KHUNG IN ẤN CHUẨN A4 -->
<div class="card p-4 shadow-sm bg-white border">
    <div class="text-center mb-4">
        <h6 class="text-uppercase fw-bold mb-1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h6>
        <div class="small fw-semibold mb-3">Độc lập - Tự do - Hạnh phúc</div>
        <hr class="w-25 mx-auto my-2">
        <h4 class="fw-bold text-uppercase mt-3 mb-1">DANH SÁCH KHÁCH THUÊ ĐĂNG KÝ TẠM TRÚ</h4>
        <div class="text-muted small">
            Thời điểm lập bảng: Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle small mb-4">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>Họ và Tên</th>
                    <th style="width: 60px;">Phòng</th>
                    <th>Nhà Trọ / Địa Chỉ</th>
                    <th>Số CCCD / CMND</th>
                    <th>Ngày Cấp / Nơi Cấp</th>
                    <th>Ngày Sinh</th>
                    <th>Quê Quán Thường Trú</th>
                    <th>Số ĐT</th>
                    <th>Biển Số Xe</th>
                </tr>
            </thead>
            <tbody>
                @php $stt = 1; @endphp
                @forelse($tenants as $t)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td class="fw-bold">{{ $t->name }}</td>
                        <td class="text-center fw-bold">P.{{ $t->currentContract?->room?->room_number }}</td>
                        <td>{{ $t->currentContract?->room?->property?->name }}</td>
                        <td><code>{{ $t->id_card_number ?: '---' }}</code></td>
                        <td>{{ $t->id_card_date ? $t->id_card_date->format('d/m/Y') : '' }} {{ $t->id_card_place ? '(' . $t->id_card_place . ')' : '' }}</td>
                        <td class="text-center">{{ $t->dob ? $t->dob->format('d/m/Y') : '---' }}</td>
                        <td>{{ $t->hometown ?: '---' }}</td>
                        <td>{{ $t->phone }}</td>
                        <td>{{ $t->vehicle_plate ?: '---' }}</td>
                    </tr>

                    <!-- Các thành viên ở cùng phòng -->
                    @if($t->currentContract && $t->currentContract->members)
                        @foreach($t->currentContract->members as $mem)
                            <tr class="table-light">
                                <td class="text-center">{{ $stt++ }}</td>
                                <td>{{ $mem->name }} <span class="badge bg-secondary" style="font-size: 0.68rem;">{{ $mem->relationship }}</span></td>
                                <td class="text-center fw-bold">P.{{ $t->currentContract?->room?->room_number }}</td>
                                <td>{{ $t->currentContract?->room?->property?->name }}</td>
                                <td><code>{{ $mem->id_card_number ?: '---' }}</code></td>
                                <td>---</td>
                                <td>---</td>
                                <td>---</td>
                                <td>{{ $mem->phone ?: '---' }}</td>
                                <td>{{ $mem->vehicle_plate ?: '---' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">Không có người lưu trú nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="row mt-4 pt-3 text-center">
        <div class="col-6">
            <div class="fw-bold mb-5">CÔNG AN PHƯỜNG / XÃ XÁC NHẬN</div>
            <div class="text-muted small fst-italic">(Ký và đóng dấu)</div>
        </div>
        <div class="col-6">
            <div class="fw-bold mb-1">CHỦ CƠ SỞ / NGƯỜI ĐẠI DIỆN</div>
            <div class="text-muted small mb-5">Hà Nội, Ngày {{ now()->format('d/m/Y') }}</div>
            <div class="text-muted small fst-italic">(Ký và ghi rõ họ tên)</div>
        </div>
    </div>
</div>
@endsection
