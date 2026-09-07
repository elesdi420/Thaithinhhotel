<?php
/**
 * Cron Worker: Tự động kéo lịch đặt phòng từ các kênh OTA (Airbnb, Agoda, Booking.com)
 * Chạy định kỳ qua Cronjob (ví dụ mỗi 15 phút)
 */

define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';
require_once _PS_ROOT_DIR_ . '/classes/OtaCalendarSync.php';

echo "[" . date('Y-m-d H:i:s') . "] === BẮT ĐẦU ĐỒNG BỘ LỊCH OTA (iCal / ICS) ===\n";

$results = OtaCalendarSyncCore::syncAllActiveFeeds();

if (empty($results)) {
    echo "- Không có kênh OTA nào đang kích hoạt.\n";
} else {
    foreach ($results as $idFeed => $res) {
        $status = $res['success'] ? 'THÀNH CÔNG' : 'LỖI';
        echo "- Feed ID #{$idFeed}: [{$status}] {$res['message']}\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . "] === KẾT THÚC TIẾN TRÌNH ĐỒNG BỘ ===\n";
