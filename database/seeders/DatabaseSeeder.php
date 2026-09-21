<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomFee;
use App\Models\RoomAsset;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\ContractMember;
use App\Models\Invoice;
use App\Models\PropertyExpense;
use App\Models\MaintenanceRequest;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $prevMonth = $now->copy()->subMonth()->month;
        $prevMonthYear = $now->copy()->subMonth()->year;

        // 1. TẠO 2 NHÀ TRỌ (MULTI-PROPERTY)
        $prop1 = Property::create([
            'name' => 'Tòa nhà Green House Cầu Giấy',
            'address' => 'Số 18 Ngõ 86 Duy Tân, Phường Dịch Vọng Hậu',
            'city' => 'Hà Nội',
            'district' => 'Cầu Giấy',
            'total_floors' => 5,
            'bank_name' => 'MBBANK',
            'bank_account_number' => '0988776655',
            'bank_account_holder' => 'NGUYEN VAN CHU',
            'electricity_meter_code' => 'EVN-HN-CG-10293',
            'water_meter_code' => 'CNHN-CG-88219',
            'description' => 'Nhà trọ 5 tầng, có thang máy, khóa cổng vân tay, camera an ninh 24/7.',
        ]);

        $prop2 = Property::create([
            'name' => 'Khu Căn hộ Mini Thanh Xuân',
            'address' => 'Số 45 Ngõ 190 Nguyễn Trãi, Phường Thượng Đình',
            'city' => 'Hà Nội',
            'district' => 'Thanh Xuân',
            'total_floors' => 4,
            'bank_name' => 'TECHCOMBANK',
            'bank_account_number' => '19035678901',
            'bank_account_holder' => 'NGUYEN VAN CHU',
            'electricity_meter_code' => 'EVN-HN-TX-55120',
            'water_meter_code' => 'CNHN-TX-44102',
            'description' => 'Căn hộ mini 4 tầng gần Ngã Tư Sở, giờ giấc tự do, sân để xe rộng.',
        ]);

        // 2. TẠO TÀI KHOẢN QUẢN TRỊ
        User::create([
            'name' => 'Chủ Nhà Trọ (Admin)',
            'email' => 'admin@nhatro.vn',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Quản lý Cầu Giấy (Manager)',
            'email' => 'quanly.caugiay@nhatro.vn',
            'password' => Hash::make('123456'),
            'role' => 'manager',
            'property_id' => $prop1->id,
        ]);

        User::create([
            'name' => 'Quản lý Thanh Xuân (Manager)',
            'email' => 'quanly.thanhxuan@nhatro.vn',
            'password' => Hash::make('123456'),
            'role' => 'manager',
            'property_id' => $prop2->id,
        ]);

        // 3. TẠO PHÒNG TRỌ CHO NHÀ CẦU GIẤY (8 phòng)
        $roomsData1 = [
            ['room_number' => '101', 'floor' => 1, 'price' => 3200000, 'area' => 22.0, 'elec_meter' => 'EM-CG-101', 'elec_init' => 1250, 'water_meter' => 'WM-CG-101', 'water_init' => 140, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'occupied'],
            ['room_number' => '102', 'floor' => 1, 'price' => 3000000, 'area' => 20.0, 'elec_meter' => 'EM-CG-102', 'elec_init' => 980, 'water_meter' => 'WM-CG-102', 'water_init' => 110, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'occupied'],
            ['room_number' => '201', 'floor' => 2, 'price' => 3500000, 'area' => 25.0, 'elec_meter' => 'EM-CG-201', 'elec_init' => 2100, 'water_meter' => 'WM-CG-201', 'water_init' => 220, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'occupied'],
            ['room_number' => '202', 'floor' => 2, 'price' => 3500000, 'area' => 25.0, 'elec_meter' => 'EM-CG-202', 'elec_init' => 1840, 'water_meter' => 'WM-CG-202', 'water_init' => 195, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'occupied'],
            ['room_number' => '301', 'floor' => 3, 'price' => 3600000, 'area' => 26.0, 'elec_meter' => 'EM-CG-301', 'elec_init' => 750, 'water_meter' => 'WM-CG-301', 'water_init' => 80, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'occupied'],
            ['room_number' => '302', 'floor' => 3, 'price' => 3600000, 'area' => 26.0, 'elec_meter' => 'EM-CG-302', 'elec_init' => 620, 'water_meter' => 'WM-CG-302', 'water_init' => 65, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'available'],
            ['room_number' => '401', 'floor' => 4, 'price' => 3400000, 'area' => 25.0, 'elec_meter' => 'EM-CG-401', 'elec_init' => 1100, 'water_meter' => 'WM-CG-401', 'water_init' => 120, 'water_type' => 'per_person', 'water_rate' => 100000, 'status' => 'available'],
            ['room_number' => '501', 'floor' => 5, 'price' => 3800000, 'area' => 30.0, 'elec_meter' => 'EM-CG-501', 'elec_init' => 450, 'water_meter' => 'WM-CG-501', 'water_init' => 50, 'water_type' => 'meter', 'water_rate' => 30000, 'status' => 'maintenance'],
        ];

        $createdRooms1 = [];
        foreach ($roomsData1 as $r) {
            $room = Room::create([
                'property_id' => $prop1->id,
                'room_number' => $r['room_number'],
                'floor' => $r['floor'],
                'price' => $r['price'],
                'area' => $r['area'],
                'max_tenants' => 3,
                'status' => $r['status'],
                'electricity_meter_number' => $r['elec_meter'],
                'initial_electricity' => $r['elec_init'],
                'electricity_rate' => 3800,
                'water_meter_number' => $r['water_meter'],
                'initial_water' => $r['water_init'],
                'water_calculation_type' => $r['water_type'],
                'water_rate' => $r['water_rate'],
                'description' => "Phòng {$r['room_number']} có ban công thoáng mát, cửa sổ lớn đón ánh sáng tự nhiên.",
            ]);

            // Cấu hình phí dịch vụ phòng
            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Tiền mạng Internet Wifi', 'fee_type' => 'fixed', 'unit_price' => 100000, 'quantity' => 1]);
            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Phí vệ sinh & rác thải', 'fee_type' => 'fixed', 'unit_price' => 40000, 'quantity' => 1]);
            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Phí thang máy & đèn hành lang', 'fee_type' => 'per_person', 'unit_price' => 50000, 'quantity' => 2]);
            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Phí gửi xe máy', 'fee_type' => 'per_unit', 'unit_price' => 100000, 'quantity' => 2]);

            // Tài sản nội thất phòng
            RoomAsset::create(['room_id' => $room->id, 'name' => 'Điều hòa Daikin 9000BTU', 'quantity' => 1, 'condition' => 'Hoạt động tốt, mát sâu', 'price' => 7500000]);
            RoomAsset::create(['room_id' => $room->id, 'name' => 'Bình nóng lạnh Rossi 20L', 'quantity' => 1, 'condition' => 'Tốt', 'price' => 2200000]);
            RoomAsset::create(['room_id' => $room->id, 'name' => 'Giường gỗ MDF 1m6x2m', 'quantity' => 1, 'condition' => 'Mới 95%', 'price' => 3000000]);
            RoomAsset::create(['room_id' => $room->id, 'name' => 'Tủ quần áo 2 cánh', 'quantity' => 1, 'condition' => 'Tốt', 'price' => 2500000]);

            $createdRooms1[$r['room_number']] = $room;
        }

        // 4. TẠO PHÒNG CHO NHÀ THANH XUÂN (4 phòng)
        $roomsData2 = [
            ['room_number' => '101', 'floor' => 1, 'price' => 2800000, 'area' => 18.0, 'elec_meter' => 'EM-TX-101', 'elec_init' => 890, 'water_meter' => 'WM-TX-101', 'water_init' => 90, 'status' => 'occupied'],
            ['room_number' => '201', 'floor' => 2, 'price' => 3200000, 'area' => 22.0, 'elec_meter' => 'EM-TX-201', 'elec_init' => 1430, 'water_meter' => 'WM-TX-201', 'water_init' => 150, 'status' => 'occupied'],
            ['room_number' => '301', 'floor' => 3, 'price' => 3200000, 'area' => 22.0, 'elec_meter' => 'EM-TX-301', 'elec_init' => 600, 'water_meter' => 'WM-TX-301', 'water_init' => 70, 'status' => 'available'],
            ['room_number' => '401', 'floor' => 4, 'price' => 3000000, 'area' => 22.0, 'elec_meter' => 'EM-TX-401', 'elec_init' => 310, 'water_meter' => 'WM-TX-401', 'water_init' => 40, 'status' => 'available'],
        ];

        $createdRooms2 = [];
        foreach ($roomsData2 as $r) {
            $room = Room::create([
                'property_id' => $prop2->id,
                'room_number' => $r['room_number'],
                'floor' => $r['floor'],
                'price' => $r['price'],
                'area' => $r['area'],
                'max_tenants' => 2,
                'status' => $r['status'],
                'electricity_meter_number' => $r['elec_meter'],
                'initial_electricity' => $r['elec_init'],
                'electricity_rate' => 3500,
                'water_meter_number' => $r['water_meter'],
                'initial_water' => $r['water_init'],
                'water_calculation_type' => 'meter',
                'water_rate' => 30000,
                'description' => "Căn hộ mini {$r['room_number']} khép kín, kệ bếp chậu rửa riêng.",
            ]);

            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Wifi tốc độ cao', 'fee_type' => 'fixed', 'unit_price' => 100000, 'quantity' => 1]);
            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Tiền rác & Vệ sinh chung', 'fee_type' => 'fixed', 'unit_price' => 50000, 'quantity' => 1]);
            RoomFee::create(['room_id' => $room->id, 'fee_name' => 'Gửi xe máy tầng 1', 'fee_type' => 'per_unit', 'unit_price' => 80000, 'quantity' => 1]);

            RoomAsset::create(['room_id' => $room->id, 'name' => 'Điều hòa Casper 9000BTU', 'quantity' => 1, 'condition' => 'Mới 100%', 'price' => 6000000]);
            RoomAsset::create(['room_id' => $room->id, 'name' => 'Bình nóng lạnh Picenza 15L', 'quantity' => 1, 'condition' => 'Tốt', 'price' => 1800000]);

            $createdRooms2[$r['room_number']] = $room;
        }

        // 5. TẠO KHÁCH THUÊ (TENANTS)
        $t1 = Tenant::create([
            'name' => 'Trần Văn Hoàng',
            'phone' => '0912345678',
            'email' => 'hoang.tran@gmail.com',
            'id_card_number' => '001098012345',
            'id_card_date' => '2021-05-15',
            'id_card_place' => 'Cục CS QLHC về TTXH',
            'dob' => '1998-08-20',
            'gender' => 'Nam',
            'hometown' => 'Nam Định',
            'vehicle_plate' => '18B2-678.90',
            'temporary_residence_status' => 'registered',
            'notes' => 'Nhân viên văn phòng, đóng tiền đúng hẹn.',
        ]);

        $t2 = Tenant::create([
            'name' => 'Nguyễn Thị Mai Linh',
            'phone' => '0987654321',
            'email' => 'mailinh99@gmail.com',
            'id_card_number' => '034199008877',
            'id_card_date' => '2022-02-10',
            'id_card_place' => 'Cục CS QLHC về TTXH',
            'dob' => '1999-11-12',
            'gender' => 'Nữ',
            'hometown' => 'Thái Bình',
            'vehicle_plate' => '17B1-456.78',
            'temporary_residence_status' => 'registered',
            'notes' => 'Kế toán công ty kiểm toán.',
        ]);

        $t3 = Tenant::create([
            'name' => 'Lê Quốc Tuấn',
            'phone' => '0978112233',
            'email' => 'tuanle.it@gmail.com',
            'id_card_number' => '026095003322',
            'id_card_date' => '2020-09-01',
            'id_card_place' => 'Công an Tỉnh Vĩnh Phúc',
            'dob' => '1995-04-05',
            'gender' => 'Nam',
            'hometown' => 'Vĩnh Phúc',
            'vehicle_plate' => '88A-123.45',
            'temporary_residence_status' => 'registered',
            'notes' => 'Kỹ sư phần mềm làm việc tại Duy Tân.',
        ]);

        $t4 = Tenant::create([
            'name' => 'Phạm Thu Trang',
            'phone' => '0904556677',
            'email' => 'thutrang.pt@gmail.com',
            'id_card_number' => '031197005544',
            'id_card_date' => '2021-12-20',
            'id_card_place' => 'Cục CS QLHC về TTXH',
            'dob' => '1997-07-18',
            'gender' => 'Nữ',
            'hometown' => 'Hải Phòng',
            'vehicle_plate' => '15B1-890.12',
            'temporary_residence_status' => 'not_registered',
            'notes' => 'Chưa nộp ảnh làm tạm trú.',
        ]);

        $t5 = Tenant::create([
            'name' => 'Đặng Minh Đức',
            'phone' => '0966778899',
            'email' => 'duc.dang@gmail.com',
            'id_card_number' => '037096001199',
            'id_card_date' => '2021-03-14',
            'id_card_place' => 'Cục CS QLHC về TTXH',
            'dob' => '1996-01-25',
            'gender' => 'Nam',
            'hometown' => 'Ninh Bình',
            'vehicle_plate' => '35B2-345.67',
            'temporary_residence_status' => 'registered',
            'notes' => 'Thuê dài hạn 1 năm.',
        ]);

        $t6 = Tenant::create([
            'name' => 'Vũ Hải Yến',
            'phone' => '0933221144',
            'email' => 'haiyen.vu@gmail.com',
            'id_card_number' => '024198007788',
            'id_card_date' => '2022-06-30',
            'id_card_place' => 'Cục CS QLHC về TTXH',
            'dob' => '1998-09-09',
            'gender' => 'Nữ',
            'hometown' => 'Bắc Ninh',
            'vehicle_plate' => '99B1-234.56',
            'temporary_residence_status' => 'registered',
            'notes' => 'Thuê phòng tại Nguyễn Trãi.',
        ]);

        // 6. TẠO HỢP ĐỒNG (CONTRACTS)
        // HĐ 1: Phòng 101 Cầu Giấy
        $c1 = Contract::create([
            'contract_code' => 'HD-2026-CG101',
            'room_id' => $createdRooms1['101']->id,
            'tenant_id' => $t1->id,
            'start_date' => $now->copy()->subMonths(3)->toDateString(),
            'end_date' => $now->copy()->addMonths(9)->toDateString(),
            'rental_price' => 3200000,
            'deposit_amount' => 3200000,
            'deposit_status' => 'held',
            'status' => 'active',
            'terms' => 'Hợp đồng 1 năm. Giữ vệ sinh chung, không làm ồn sau 23h.',
        ]);
        ContractMember::create(['contract_id' => $c1->id, 'name' => 'Trần Văn Nam', 'phone' => '0912999888', 'id_card_number' => '001099014455', 'relationship' => 'Em trai', 'vehicle_plate' => '18B2-111.22']);

        // HĐ 2: Phòng 102 Cầu Giấy
        $c2 = Contract::create([
            'contract_code' => 'HD-2026-CG102',
            'room_id' => $createdRooms1['102']->id,
            'tenant_id' => $t2->id,
            'start_date' => $now->copy()->subMonths(5)->toDateString(),
            'end_date' => $now->copy()->addDays(20)->toDateString(), // Sắp hết hạn trong 20 ngày
            'rental_price' => 3000000,
            'deposit_amount' => 3000000,
            'deposit_status' => 'held',
            'status' => 'expiring_soon',
            'terms' => 'Hợp đồng 6 tháng. Đã báo sắp gia hạn tiếp.',
        ]);

        // HĐ 3: Phòng 201 Cầu Giấy
        $c3 = Contract::create([
            'contract_code' => 'HD-2026-CG201',
            'room_id' => $createdRooms1['201']->id,
            'tenant_id' => $t3->id,
            'start_date' => $now->copy()->subMonths(2)->toDateString(),
            'end_date' => $now->copy()->addMonths(10)->toDateString(),
            'rental_price' => 3500000,
            'deposit_amount' => 3500000,
            'deposit_status' => 'held',
            'status' => 'active',
        ]);
        ContractMember::create(['contract_id' => $c3->id, 'name' => 'Nguyễn Tuấn Anh', 'phone' => '0978998811', 'id_card_number' => '001099022334', 'relationship' => 'Bạn cùng phòng', 'vehicle_plate' => '29E1-445.66']);
        ContractMember::create(['contract_id' => $c3->id, 'name' => 'Hoàng Văn Dũng', 'phone' => '0978223344', 'id_card_number' => '001099033445', 'relationship' => 'Bạn cùng phòng', 'vehicle_plate' => '29B1-889.90']);

        // HĐ 4: Phòng 202 Cầu Giấy
        $c4 = Contract::create([
            'contract_code' => 'HD-2026-CG202',
            'room_id' => $createdRooms1['202']->id,
            'tenant_id' => $t4->id,
            'start_date' => $now->copy()->subMonths(1)->toDateString(),
            'end_date' => $now->copy()->addMonths(11)->toDateString(),
            'rental_price' => 3500000,
            'deposit_amount' => 3500000,
            'deposit_status' => 'held',
            'status' => 'active',
        ]);
        ContractMember::create(['contract_id' => $c4->id, 'name' => 'Đỗ Thùy Linh', 'phone' => '0904112233', 'id_card_number' => '001099055667', 'relationship' => 'Bạn ở ghép', 'vehicle_plate' => '30F4-123.45']);

        // HĐ 5: Phòng 301 Cầu Giấy
        $c5 = Contract::create([
            'contract_code' => 'HD-2026-CG301',
            'room_id' => $createdRooms1['301']->id,
            'tenant_id' => $t5->id,
            'start_date' => $now->copy()->subMonths(4)->toDateString(),
            'end_date' => $now->copy()->addMonths(8)->toDateString(),
            'rental_price' => 3600000,
            'deposit_amount' => 3600000,
            'deposit_status' => 'held',
            'status' => 'active',
        ]);

        // HĐ 6: Phòng 101 Thanh Xuân
        $c6 = Contract::create([
            'contract_code' => 'HD-2026-TX101',
            'room_id' => $createdRooms2['101']->id,
            'tenant_id' => $t6->id,
            'start_date' => $now->copy()->subMonths(2)->toDateString(),
            'end_date' => $now->copy()->addMonths(4)->toDateString(),
            'rental_price' => 2800000,
            'deposit_amount' => 2800000,
            'deposit_status' => 'held',
            'status' => 'active',
        ]);

        $padMonth = str_pad($currentMonth, 2, '0', STR_PAD_LEFT);

        // Hóa đơn 1: Phòng 101 Cầu Giấy - ĐÃ THANH TOÁN
        $inv1 = Invoice::create([
            'invoice_code' => "INV-{$currentYear}{$padMonth}-P101",
            'contract_id' => $c1->id,
            'room_id' => $createdRooms1['101']->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'from_date' => $now->copy()->startOfMonth()->toDateString(),
            'to_date' => $now->copy()->endOfMonth()->toDateString(),
            'due_date' => $now->copy()->startOfMonth()->addDays(5)->toDateString(),
            'electricity_old' => 1250,
            'electricity_new' => 1370,
            'electricity_usage' => 120,
            'electricity_rate' => 3800,
            'electricity_total' => 456000,
            'water_old' => 140,
            'water_new' => 148,
            'water_usage' => 8,
            'water_rate' => 30000,
            'water_total' => 240000,
            'room_price' => 3200000,
            'fees_detail' => [
                ['name' => 'Wifi Internet', 'amount' => 100000],
                ['name' => 'Vệ sinh & Rác', 'amount' => 40000],
                ['name' => 'Thang máy (2 người)', 'amount' => 100000],
                ['name' => 'Gửi xe (2 xe)', 'amount' => 200000],
            ],
            'other_fees' => 440000,
            'discount' => 0,
            'total_amount' => 4336000,
            'paid_amount' => 4336000,
            'remaining_amount' => 0,
            'status' => 'paid',
            'payment_method' => 'transfer',
            'paid_at' => $now->copy()->startOfMonth()->addDays(3),
            'notes' => 'Khách đã chuyển khoản qua VietQR MB Bank.',
        ]);

        // Hóa đơn 2: Phòng 102 Cầu Giấy - QUÁ HẠN (OVERDUE 5 NGÀY) - Trọng tâm nhắc nợ!
        $inv2 = Invoice::create([
            'invoice_code' => "INV-{$currentYear}{$padMonth}-P102",
            'contract_id' => $c2->id,
            'room_id' => $createdRooms1['102']->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'from_date' => $now->copy()->startOfMonth()->toDateString(),
            'to_date' => $now->copy()->endOfMonth()->toDateString(),
            'due_date' => $now->copy()->subDays(5)->toDateString(), // Đã quá hạn 5 ngày
            'electricity_old' => 980,
            'electricity_new' => 1095,
            'electricity_usage' => 115,
            'electricity_rate' => 3800,
            'electricity_total' => 437000,
            'water_old' => 110,
            'water_new' => 116,
            'water_usage' => 6,
            'water_rate' => 30000,
            'water_total' => 180000,
            'room_price' => 3000000,
            'fees_detail' => [
                ['name' => 'Wifi Internet', 'amount' => 100000],
                ['name' => 'Vệ sinh & Rác', 'amount' => 40000],
                ['name' => 'Thang máy (1 người)', 'amount' => 50000],
                ['name' => 'Gửi xe (1 xe)', 'amount' => 100000],
            ],
            'other_fees' => 290000,
            'discount' => 0,
            'total_amount' => 3907000,
            'paid_amount' => 0,
            'remaining_amount' => 3907000,
            'status' => 'overdue',
            'notes' => 'Khách hẹn cuối tuần nhận lương chuyển.',
        ]);

        // Hóa đơn 3: Phòng 201 Cầu Giấy - SẮP ĐẾN HẠN (CÒN 2 NGÀY) - Trọng tâm cảnh báo!
        $inv3 = Invoice::create([
            'invoice_code' => "INV-{$currentYear}{$padMonth}-P201",
            'contract_id' => $c3->id,
            'room_id' => $createdRooms1['201']->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'from_date' => $now->copy()->startOfMonth()->toDateString(),
            'to_date' => $now->copy()->endOfMonth()->toDateString(),
            'due_date' => $now->copy()->addDays(2)->toDateString(), // Hạn còn 2 ngày
            'electricity_old' => 2100,
            'electricity_new' => 2240,
            'electricity_usage' => 140,
            'electricity_rate' => 3800,
            'electricity_total' => 532000,
            'water_old' => 220,
            'water_new' => 229,
            'water_usage' => 9,
            'water_rate' => 30000,
            'water_total' => 270000,
            'room_price' => 3500000,
            'fees_detail' => [
                ['name' => 'Wifi Internet', 'amount' => 100000],
                ['name' => 'Vệ sinh & Rác', 'amount' => 40000],
                ['name' => 'Thang máy (2 người)', 'amount' => 100000],
                ['name' => 'Gửi xe (2 xe)', 'amount' => 200000],
            ],
            'other_fees' => 440000,
            'discount' => 0,
            'total_amount' => 4742000,
            'paid_amount' => 0,
            'remaining_amount' => 4742000,
            'status' => 'unpaid',
            'notes' => 'Chưa thanh toán.',
        ]);

        // Hóa đơn 4: Phòng 101 Thanh Xuân - ĐÃ TRẢ MỘT PHẦN
        $inv4 = Invoice::create([
            'invoice_code' => "INV-{$currentYear}{$padMonth}-TX101",
            'contract_id' => $c6->id,
            'room_id' => $createdRooms2['101']->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'from_date' => $now->copy()->startOfMonth()->toDateString(),
            'to_date' => $now->copy()->endOfMonth()->toDateString(),
            'due_date' => $now->copy()->addDays(3)->toDateString(),
            'electricity_old' => 890,
            'electricity_new' => 985,
            'electricity_usage' => 95,
            'electricity_rate' => 3500,
            'electricity_total' => 332500,
            'water_old' => 90,
            'water_new' => 96,
            'water_usage' => 6,
            'water_rate' => 30000,
            'water_total' => 180000,
            'room_price' => 2800000,
            'fees_detail' => [
                ['name' => 'Wifi tốc độ cao', 'amount' => 100000],
                ['name' => 'Vệ sinh rác', 'amount' => 50000],
                ['name' => 'Gửi xe', 'amount' => 80000],
            ],
            'other_fees' => 230000,
            'discount' => 0,
            'total_amount' => 3542500,
            'paid_amount' => 2000000,
            'remaining_amount' => 1542500,
            'status' => 'partially_paid',
            'payment_method' => 'transfer',
            'paid_at' => $now->copy()->subDays(1),
            'notes' => 'Khách đã cọc trước 2 triệu, hẹn nốt phần còn lại 15/tháng.',
        ]);

        // 7.1 Ý KIẾN / KHIẾU NẠI HÓA ĐƠN TỪ KHÁCH THUÊ
        \App\Models\InvoiceFeedback::create([
            'invoice_id' => $inv2->id,
            'tenant_id' => $t2->id,
            'feedback_type' => 'electricity',
            'content' => 'Chào chú chủ nhà, tháng này cháu về quê mất 1 tuần mà số điện nhảy lên 115 kWh, chú xem lại giúp cháu xem công tơ điện có bị quay nhầm không ạ.',
            'status' => 'pending',
        ]);

        // 8. TẠO CHI PHÍ ĐẦU RA CHO NHÀ TRỌ (CHI PHÍ TRẢ NHÀ NƯỚC & VẬN HÀNH TÁCH RIÊNG)
        // Chi phí Nhà Cầu Giấy
        PropertyExpense::create([
            'property_id' => $prop1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'electricity_evn',
            'title' => 'Hóa đơn Điện tổng EVN Cầu Giấy (Tháng ' . $currentMonth . ')',
            'amount' => 2650000,
            'total_meter_usage' => 890, // 890 kWh tổng cả tòa
            'payment_date' => $now->copy()->subDays(2)->toDateString(),
            'paid_by' => 'Chủ nhà',
            'notes' => 'Đã nộp online qua app EVN Hà Nội. Mã KH: EVN-HN-CG-10293.',
        ]);

        PropertyExpense::create([
            'property_id' => $prop1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'water_supply',
            'title' => 'Hóa đơn Nước sinh hoạt tổng (Tháng ' . $currentMonth . ')',
            'amount' => 980000,
            'total_meter_usage' => 52, // 52 m3 tổng cả tòa
            'payment_date' => $now->copy()->subDays(3)->toDateString(),
            'paid_by' => 'Chủ nhà',
            'notes' => 'Thanh toán cho Công ty Cổ phần Nước sạch Cầu Giấy.',
        ]);

        PropertyExpense::create([
            'property_id' => $prop1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'state_tax',
            'title' => 'Thuế môn bài & Thuế khoán kinh doanh phòng trọ Quý 3 (Chi cục Thuế CG)',
            'amount' => 1500000,
            'payment_date' => $now->copy()->subDays(10)->toDateString(),
            'paid_by' => 'Chủ nhà',
            'notes' => 'Nộp tiền vào Kho bạc Nhà nước quận Cầu Giấy theo biên lai số 0049281.',
        ]);

        PropertyExpense::create([
            'property_id' => $prop1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'internet_bill',
            'title' => 'Cáp quang FPT Doanh nghiệp 500Mbps cho cả tòa nhà',
            'amount' => 550000,
            'payment_date' => $now->copy()->subDays(8)->toDateString(),
            'paid_by' => 'Chủ nhà',
            'notes' => 'Gói cáp quang có IP tĩnh phát 5 bộ Mesh wifi các tầng.',
        ]);

        PropertyExpense::create([
            'property_id' => $prop1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'waste_collection',
            'title' => 'Hợp đồng thu gom rác thải dân sinh phường Dịch Vọng Hậu',
            'amount' => 250000,
            'payment_date' => $now->copy()->subDays(15)->toDateString(),
            'paid_by' => 'Quản lý',
            'notes' => 'Đóng định kỳ 6 tháng/lần cho Hợp tác xã Môi trường Đô thị.',
        ]);

        PropertyExpense::create([
            'property_id' => $prop1->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'maintenance_repair',
            'title' => 'Bảo trì thang máy định kỳ & Thay rơ-le phao điện máy bơm',
            'amount' => 850000,
            'payment_date' => $now->copy()->subDays(6)->toDateString(),
            'paid_by' => 'Quản lý',
            'notes' => 'Thợ thang máy kiểm tra dầu và phanh an toàn.',
        ]);

        // Chi phí Nhà Thanh Xuân
        PropertyExpense::create([
            'property_id' => $prop2->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'electricity_evn',
            'title' => 'Hóa đơn Điện tổng EVN Thanh Xuân',
            'amount' => 1420000,
            'total_meter_usage' => 490,
            'payment_date' => $now->copy()->subDays(4)->toDateString(),
            'paid_by' => 'Chủ nhà',
            'notes' => 'Nộp tiền điện EVN mã KH: EVN-HN-TX-55120.',
        ]);

        PropertyExpense::create([
            'property_id' => $prop2->id,
            'month' => $currentMonth,
            'year' => $currentYear,
            'expense_type' => 'state_tax',
            'title' => 'Thuế kinh doanh phòng trọ Quý 3 Chi cục Thuế Thanh Xuân',
            'amount' => 1000000,
            'payment_date' => $now->copy()->subDays(12)->toDateString(),
            'paid_by' => 'Chủ nhà',
            'notes' => 'Biên lai nộp thuế điện tử eTax.',
        ]);

        // 9. TẠO YÊU CẦU SỰ CỐ / SỬA CHỮA (MAINTENANCE REQUESTS)
        MaintenanceRequest::create([
            'room_id' => $createdRooms1['501']->id,
            'title' => 'Sửa vòi hoa sen rỉ nước và sơn lại mảng tường phòng 501',
            'description' => 'Khách cũ vừa trả phòng, cần thay củ sen mới và sơn dặm lại tường trước khi cho khách mới vào.',
            'cost' => 650000,
            'status' => 'in_progress',
            'reported_date' => $now->copy()->subDays(2)->toDateString(),
        ]);

        MaintenanceRequest::create([
            'room_id' => $createdRooms1['102']->id,
            'title' => 'Bóng đèn tuýp LED ban công bị chớp nháy',
            'description' => 'Khách phòng 102 báo đèn ban công bật lúc được lúc không.',
            'cost' => 120000,
            'status' => 'pending',
            'reported_date' => $now->copy()->subDays(1)->toDateString(),
        ]);

        MaintenanceRequest::create([
            'room_id' => $createdRooms2['101']->id,
            'title' => 'Vệ sinh và bảo dưỡng điều hòa Casper',
            'description' => 'Khách báo điều hòa kêu rè rè và yếu lạnh. Đã gọi thợ xịt rửa lưới lọc và nạp thêm gas.',
            'cost' => 250000,
            'status' => 'completed',
            'reported_date' => $now->copy()->subDays(10)->toDateString(),
            'resolved_date' => $now->copy()->subDays(9)->toDateString(),
        ]);
    }
}
