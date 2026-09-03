<?php
define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== CHUYỂN TOÀN BỘ HỆ THỐNG SANG GIAO DIỆN TIẾNG VIỆT ===\n";

$idLangVn = 2; // tiếng Việt

// 1. Cấu hình ngôn ngữ mặc định trong qlo_configuration
Configuration::updateValue('PS_LANG_DEFAULT', $idLangVn);
Configuration::updateValue('PS_DETECT_LANG', 0); // Không tự bắt ngôn ngữ trình duyệt, luôn ưu tiên tiếng Việt
Configuration::updateValue('PS_LOCALE_LANGUAGE', 'vi');
Configuration::updateValue('PS_LOCALE_COUNTRY', 'vn');

echo "1. Đã đặt ngôn ngữ mặc định hệ thống: ID 2 (tiếng Việt)\n";

// 2. Chuyển toàn bộ tài khoản nhân viên (Admin, Lễ tân, Quản lý) sang tiếng Việt
Db::getInstance()->execute("UPDATE " . _DB_PREFIX_ . "employee SET id_lang = " . (int) $idLangVn);
echo "2. Đã cập nhật toàn bộ tài khoản nhân viên sang tiếng Việt (id_lang = 2)\n";

// 3. Chuyển toàn bộ tài khoản khách hàng sang tiếng Việt
Db::getInstance()->execute("UPDATE " . _DB_PREFIX_ . "customer SET id_lang = " . (int) $idLangVn);
echo "3. Đã cập nhật tài khoản khách hàng sang tiếng Việt\n";

// 4. Cập nhật và việt hóa danh mục menu quản trị (Admin Tabs)
$tabTranslations = [
    // Menu gốc
    'AdminDashboard' => 'Bảng điều khiển',
    'AdminParentOrders' => 'Quản lý Đơn hàng',
    'AdminOrders' => 'Đơn đặt phòng',
    'AdminInvoices' => 'Hóa đơn',
    'AdminOrderMessage' => 'Tin nhắn đơn hàng',
    'AdminParentCustomer' => 'Khách hàng',
    'AdminCustomers' => 'Danh sách khách hàng',
    'AdminAddresses' => 'Địa chỉ khách hàng',
    'AdminPriceRule' => 'Khuyến mại & Giảm giá',
    'AdminParentModules' => 'Module & Dịch vụ',
    'AdminParentPreferences' => 'Tùy chọn cấu hình',
    'AdminTools' => 'Thông số nâng cao',
    'AdminAdmin' => 'Quản trị hệ thống',
    'AdminEmployees' => 'Tài khoản nhân viên',
    'AdminProfiles' => 'Phân quyền nhân viên',
    'AdminAccess' => 'Chi tiết quyền hạn',
    'AdminStats' => 'Báo cáo Thống kê',
    
    // Phân hệ Quản lý Khách sạn (Hotel Reservation System)
    'AdminHotelReservationSystemManagement' => 'Quản lý Khách sạn',
    'AdminAddHotel' => 'Quản lý Chi nhánh & Phòng',
    'AdminHotelRoomsBooking' => 'Lịch phòng & Đặt phòng',
    'AdminHotelAmenities' => 'Quản lý Tiện ích phòng',
    'AdminOrderRefundRules' => 'Quy định Hoàn hủy phòng',
    'AdminOrderRefundRequests' => 'Yêu cầu Hoàn hủy',
    'AdminHotelConfigurationSetting' => 'Cài đặt Khách sạn',
    'AdminHotelGeneralSettings' => 'Cấu hình Chung',
    'AdminHotelFeaturePricesSettings' => 'Bảng giá Theo mùa / Ngày lễ',
    'AdminRoomTypeGlobalDemand' => 'Dịch vụ & Nhu cầu bổ sung',
    'AdminBookingDocument' => 'Chứng từ Đặt phòng',
    'AdminHotelImageCategory' => 'Danh mục Hình ảnh',
    'AdminQloappsChannelManagerConnector' => 'Đồng bộ Kênh bán phòng (OTA)',
];

foreach ($tabTranslations as $className => $vnName) {
    $idTab = (int) Db::getInstance()->getValue("SELECT id_tab FROM " . _DB_PREFIX_ . "tab WHERE class_name = '" . pSQL($className) . "'");
    if ($idTab) {
        Db::getInstance()->execute("
            REPLACE INTO " . _DB_PREFIX_ . "tab_lang (`id_tab`, `id_lang`, `name`)
            VALUES ({$idTab}, {$idLangVn}, '" . pSQL($vnName) . "')
        ");
    }
}
echo "4. Đã hoàn tất Việt hóa toàn bộ menu quản trị Back Office\n";

// 5. Làm mới toàn bộ cache template và Smarty
Context::getContext()->smarty->clearAllCache();
Tools::clearCache();

echo "\n============================================\n";
echo "CHUYỂN GIAO DIỆN TIẾNG VIỆT HOÀN TẤT 100%!\n";
echo "============================================\n";
