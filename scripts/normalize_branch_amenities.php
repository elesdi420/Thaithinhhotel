<?php
define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== 1. CHUẨN HÓA TIỆN ÍCH THỰC TẾ CHO 3 CHI NHÁNH (STORY 4.1) ===\n";

$amenities = [
    [
        'en' => 'High-speed Free Wi-Fi',
        'vn' => 'Wifi tốc độ cao (Miễn phí)',
    ],
    [
        'en' => 'Air Conditioning',
        'vn' => 'Điều hòa nhiệt độ 2 chiều',
    ],
    [
        'en' => 'Smart Flat-screen TV',
        'vn' => 'Smart TV truyền hình cáp',
    ],
    [
        'en' => 'Daily Breakfast',
        'vn' => 'Bữa sáng hàng ngày',
    ],
    [
        'en' => 'Daily Housekeeping',
        'vn' => 'Dọn phòng hàng ngày',
    ],
    [
        'en' => 'Complimentary Water & Tea',
        'vn' => 'Nước khoáng & Trà miễn phí',
    ],
    [
        'en' => '24/7 Front Desk',
        'vn' => 'Lễ tân phục vụ 24/7',
    ],
    [
        'en' => 'Secured Parking',
        'vn' => 'Bãi đỗ xe an toàn',
    ],
    [
        'en' => 'Hairdryer & Toiletries',
        'vn' => 'Máy sấy tóc & Đồ vệ sinh cá nhân',
    ],
    [
        'en' => 'Minibar & Refrigerator',
        'vn' => 'Tủ lạnh mini & Đồ uống',
    ]
];

// Xoá toàn bộ tiện ích ảo gán vào chi nhánh trước đó
Db::getInstance()->execute("TRUNCATE TABLE " . _DB_PREFIX_ . "htl_branch_amenity");
echo "- Đã xóa sạch tiện ích ảo (Casino, Disco, Amphitheatre...) khỏi bảng chi nhánh.\n";

$amenityIds = [];
foreach ($amenities as $idx => $am) {
    $idAmenity = $idx + 1;
    // Đảm bảo bản ghi amenity tồn tại
    Db::getInstance()->execute("
        REPLACE INTO " . _DB_PREFIX_ . "htl_amenity (`id_amenity`, `active`, `date_add`, `date_upd`)
        VALUES ({$idAmenity}, 1, NOW(), NOW())
    ");

    // Ghi tên tiếng Anh (id_lang = 1) và tiếng Việt (id_lang = 2)
    Db::getInstance()->execute("
        REPLACE INTO " . _DB_PREFIX_ . "htl_amenity_lang (`id_amenity`, `id_lang`, `name`)
        VALUES ({$idAmenity}, 1, '" . pSQL($am['en']) . "')
    ");
    Db::getInstance()->execute("
        REPLACE INTO " . _DB_PREFIX_ . "htl_amenity_lang (`id_amenity`, `id_lang`, `name`)
        VALUES ({$idAmenity}, 2, '" . pSQL($am['vn']) . "')
    ");

    $amenityIds[] = $idAmenity;
}

// Gán đồng bộ cho cả 3 chi nhánh (id_hotel: 1, 2, 3)
$branches = [1, 2, 3];
foreach ($branches as $hotelId) {
    foreach ($amenityIds as $amId) {
        Db::getInstance()->execute("
            INSERT INTO " . _DB_PREFIX_ . "htl_branch_amenity (`id_hotel`, `amenity_id`, `is_featured`, `date_add`, `date_upd`)
            VALUES ({$hotelId}, {$amId}, 1, NOW(), NOW())
        ");
    }
    echo "- Đã gán " . count($amenityIds) . " tiện ích chuẩn cho Chi nhánh ID {$hotelId}\n";
}

echo "\n=== 2. VIỆT HÓA KHỐI TÍNH NĂNG NỔI BẬT TRANG CHỦ (FEATURES BLOCK) ===\n";

$features = [
    1 => [
        'en' => ['title' => 'Luxurious Rooms', 'desc' => 'Modern and cozy accommodation at central prime locations in HCMC, Da Nang, and Hanoi.'],
        'vn' => ['title' => 'Phòng nghỉ tiện nghi', 'desc' => 'Không gian lưu trú hiện đại, tiện nghi đầy đủ tại trung tâm TP.HCM, Đà Nẵng và Hà Nội.']
    ],
    2 => [
        'en' => ['title' => 'Prime Locations', 'desc' => 'Centrally located properties close to famous tourist spots and convenient transportation.'],
        'vn' => ['title' => 'Vị trí đắc địa', 'desc' => 'Tọa lạc tại các vị trí trung tâm, gần các điểm tham quan du lịch nổi tiếng và thuận tiện di chuyển.']
    ],
    3 => [
        'en' => ['title' => 'Attentive 24/7 Service', 'desc' => 'Dedicated front desk team with daily housekeeping and round-the-clock guest support.'],
        'vn' => ['title' => 'Dịch vụ tận tâm 24/7', 'desc' => 'Đội ngũ lễ tân chu đáo, hỗ trợ khách hàng 24/7 và phục vụ bữa sáng hàng ngày.']
    ],
    4 => [
        'en' => ['title' => 'Instant VietQR Booking', 'desc' => 'Direct booking with best guaranteed rates and automated QR payment confirmation.'],
        'vn' => ['title' => 'Đặt phòng & Thanh toán QR', 'desc' => 'Đặt phòng trực tiếp với giá tốt nhất thị trường, quét mã VietQR tự động xác nhận đơn tức thì.']
    ]
];

foreach ($features as $idBlock => $data) {
    Db::getInstance()->execute("
        UPDATE " . _DB_PREFIX_ . "htl_features_block_data SET active = 1 WHERE id_features_block = {$idBlock}
    ");
    Db::getInstance()->execute("
        REPLACE INTO " . _DB_PREFIX_ . "htl_features_block_data_lang (`id_features_block`, `id_lang`, `feature_title`, `feature_description`)
        VALUES ({$idBlock}, 1, '" . pSQL($data['en']['title']) . "', '" . pSQL($data['en']['desc']) . "')
    ");
    Db::getInstance()->execute("
        REPLACE INTO " . _DB_PREFIX_ . "htl_features_block_data_lang (`id_features_block`, `id_lang`, `feature_title`, `feature_description`)
        VALUES ({$idBlock}, 2, '" . pSQL($data['vn']['title']) . "', '" . pSQL($data['vn']['desc']) . "')
    ");
}
echo "- Đã cập nhật 4 khối tính năng trang chủ thuần Việt & chuyên nghiệp.\n";

// 3. Clear Cache
Context::getContext()->smarty->clearAllCache();
Tools::clearCache();
echo "- Đã xóa Smarty Cache thành công!\n";

echo "\n=== HOÀN TẤT CHUẨN HÓA TIỆN ÍCH VÀ GIAO DIỆN (STORY 4.1) ===\n";
