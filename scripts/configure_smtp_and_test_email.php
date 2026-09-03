<?php
/**
 * Script configuring SMTP and testing Booking Engine & Confirmation Email
 */

define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== STEP 1: CONFIGURE SMTP SETTINGS IN QLOAPPS ===\n";

Configuration::updateValue('PS_MAIL_METHOD', 2); // 2 = Use SMTP
Configuration::updateValue('PS_MAIL_SERVER', 'mailpit');
Configuration::updateValue('PS_MAIL_SMTP_PORT', 1025);
Configuration::updateValue('PS_MAIL_USER', '');
Configuration::updateValue('PS_MAIL_PASSWD', '');
Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', 'off');
Configuration::updateValue('PS_SHOP_EMAIL', 'booking@hotel.local');
Configuration::updateValue('PS_MAIL_TYPE', 3); // Both HTML and text

echo "SMTP Method: " . Configuration::get('PS_MAIL_METHOD') . " (2 = SMTP)\n";
echo "SMTP Host: " . Configuration::get('PS_MAIL_SERVER') . ":" . Configuration::get('PS_MAIL_SMTP_PORT') . "\n";
echo "Sender: " . Configuration::get('PS_SHOP_EMAIL') . "\n";

echo "\n=== STEP 2: TEST BOOKING ENGINE SEARCH (FR-8) ===\n";

$bookingObj = new HotelBookingDetail();
$dateFrom = date('Y-m-d', strtotime('+7 days'));
$dateTo = date('Y-m-d', strtotime('+9 days'));

$hotels = [
    1 => 'Cơ sở 1 - Trung tâm',
    2 => 'Cơ sở 2 - Bờ biển',
    3 => 'Cơ sở 3 - Phố cổ'
];

$allSearchSuccess = true;

foreach ($hotels as $idHotel => $hotelName) {
    $searchParams = [
        'date_from' => $dateFrom,
        'date_to' => $dateTo,
        'hotel_id' => $idHotel,
        'only_active_hotel' => 1,
        'only_active_roomtype' => 1
    ];
    $data = $bookingObj->getBookingData($searchParams);
    $totalRooms = isset($data['stats']['total_rooms']) ? (int) $data['stats']['total_rooms'] : 0;
    $availRooms = isset($data['stats']['num_avail']) ? (int) $data['stats']['num_avail'] : 0;
    
    echo "  - [{$hotelName}] Từ {$dateFrom} đến {$dateTo}:\n";
    echo "      Tổng số phòng: {$totalRooms} | Phòng trống khả dụng: {$availRooms}\n";
    
    if ($availRooms <= 0) {
        $allSearchSuccess = false;
    }
}

if ($allSearchSuccess) {
    echo "==> Booking Engine Search: PASS (Cả 3 cơ sở đều tính đúng phòng trống!)\n";
} else {
    echo "==> Booking Engine Search: FAILED (Không tìm thấy phòng trống!)\n";
}

echo "\n=== STEP 3: SEND BOOKING CONFIRMATION EMAIL VIA SMTP (FR-10) ===\n";

$idLang = (int) Configuration::get('PS_LANG_DEFAULT');
$template = 'order_conf';
$subject = 'Xác nhận đặt phòng thành công - Mã #BOOK-2026-001';
$to = 'khachhang@example.com';
$toName = 'Nguyễn Văn A';

$templateVars = [
    '{firstname}' => 'Nguyễn',
    '{lastname}' => 'Văn A',
    '{email}' => $to,
    '{delivery_block_html}' => 'Cơ sở 1 - Trung tâm (123 Lê Lợi, Q1, TP.HCM)',
    '{invoice_block_html}' => 'Phòng 101 (Deluxe Room) - 2 đêm: 10/09/2026 đến 12/09/2026',
    '{delivery_block_txt}' => 'Cơ sở 1 - Trung tâm',
    '{invoice_block_txt}' => 'Phòng 101 - Deluxe Room',
    '{order_name}' => 'BOOK-2026-001',
    '{date}' => date('d/m/Y H:i'),
    '{payment}' => 'Chuyển khoản VietQR',
    '{total_paid}' => '1,600,000 ₫',
    '{total_products}' => '1,600,000 ₫',
    '{total_discounts}' => '0 ₫',
    '{total_shipping}' => '0 ₫',
    '{total_wrapping}' => '0 ₫',
    '{total_tax_paid}' => '0 ₫',
    '{products}' => '<tr><td>Deluxe Room (Phòng 101)</td><td>2 đêm</td><td>800,000 ₫/đêm</td><td>1,600,000 ₫</td></tr>',
    '{products_txt}' => 'Deluxe Room (Phòng 101) - 2 đêm x 800,000 ₫ = 1,600,000 ₫'
];

$mailSent = Mail::send(
    $idLang,
    $template,
    $subject,
    $templateVars,
    $to,
    $toName,
    null, // From email (defaults to PS_SHOP_EMAIL)
    null, // From name (defaults to PS_SHOP_NAME)
    null, // File attachment
    null, // Mode smtp
    _PS_MAIL_DIR_,
    false, // Die on error
    null
);

if ($mailSent) {
    echo "==> Gửi email qua SMTP: THÀNH CÔNG!\n";
    echo "    Người nhận: {$to}\n";
    echo "    Tiêu đề: {$subject}\n";
    echo "    Đã chuyển tiếp tới Mailpit (localhost:1025)\n";
} else {
    echo "==> Gửi email: THẤT BẠI!\n";
}

echo "\n============================================\n";
echo "STORY 1.4 TEST EXECUTION COMPLETED!\n";
echo "============================================\n";
