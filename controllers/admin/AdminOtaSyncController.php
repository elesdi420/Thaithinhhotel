<?php
/**
 * AdminOtaSyncControllerCore
 * Quản lý Đồng bộ Lịch 2 Chiều với OTA (Airbnb, Agoda, Booking.com) qua chuẩn iCal/ICS
 */

require_once _PS_ROOT_DIR_ . '/classes/OtaCalendarSync.php';

class AdminOtaSyncControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Đồng bộ Kênh Bán Phòng (OTA iCal)');
        $this->page_header_toolbar_btn = [];
        parent::initPageHeaderToolbar();
    }

    public function postProcess()
    {
        parent::postProcess();

        // 1. Thêm feed OTA mới
        if (Tools::isSubmit('submitAddOtaFeed')) {
            $idHotel = (int) Tools::getValue('id_hotel');
            $channelName = trim(Tools::getValue('channel_name'));
            $feedUrl = trim(Tools::getValue('feed_url'));

            if ($idHotel <= 0 || empty($channelName) || empty($feedUrl)) {
                $this->errors[] = $this->l('Vui lòng điền đầy đủ Chi nhánh, Tên kênh và Đường dẫn iCal (.ics).');
                return;
            }

            if (!filter_var($feedUrl, FILTER_VALIDATE_URL) && !preg_match('/^https?:\/\//i', $feedUrl)) {
                $this->errors[] = $this->l('Đường dẫn URL iCal không hợp lệ.');
                return;
            }

            Db::getInstance()->execute("
                INSERT INTO " . _DB_PREFIX_ . "ota_sync_feed 
                (`id_hotel`, `id_room_type`, `channel_name`, `feed_url`, `active`, `date_add`, `date_upd`)
                VALUES ({$idHotel}, 0, '" . pSQL($channelName) . "', '" . pSQL($feedUrl) . "', 1, NOW(), NOW())
            ");

            $newId = Db::getInstance()->Insert_ID();
            // Tự động đồng bộ ngay lần đầu
            $res = OtaCalendarSyncCore::syncFeed($newId);
            if ($res['success']) {
                $this->confirmations[] = $this->l('Đã thêm kênh OTA và đồng bộ thành công: ' . $res['message']);
            } else {
                $this->warnings[] = $this->l('Đã lưu kênh OTA nhưng chưa tải được lịch: ' . $res['message']);
            }
        }

        // 2. Xóa feed OTA
        if (Tools::isSubmit('delete_feed')) {
            $idFeed = (int) Tools::getValue('delete_feed');
            if ($idFeed > 0) {
                OtaCalendarSyncCore::deleteFeed($idFeed);
                $this->confirmations[] = $this->l('Đã xóa kênh kết nối OTA và mở lại các ngày phòng liên quan.');
            }
        }

        // 3. Đồng bộ 1 feed
        if (Tools::isSubmit('sync_feed')) {
            $idFeed = (int) Tools::getValue('sync_feed');
            if ($idFeed > 0) {
                $res = OtaCalendarSyncCore::syncFeed($idFeed);
                if ($res['success']) {
                    $this->confirmations[] = $res['message'];
                } else {
                    $this->errors[] = $res['message'];
                }
            }
        }

        // 4. Đồng bộ toàn bộ feeds
        if (Tools::isSubmit('submitSyncAllOta')) {
            $results = OtaCalendarSyncCore::syncAllActiveFeeds();
            $totalSuccess = 0;
            foreach ($results as $r) {
                if ($r['success']) {
                    $totalSuccess += $r['count'];
                }
            }
            $this->confirmations[] = "Đã đồng bộ toàn bộ các kênh kết nối thành công (Tổng cộng {$totalSuccess} khoảng ngày đã khóa).";
        }
    }

    public function renderView()
    {
        $idProfile = (int) $this->context->employee->id_profile;

        // Lấy danh sách các chi nhánh được phép truy cập
        $permittedHotels = [];
        if (class_exists('HotelBranchInformation')) {
            $permittedHotels = HotelBranchInformation::getProfileAccessedHotels($idProfile);
        }
        if (empty($permittedHotels)) {
            $permittedHotels = [1, 2, 3];
        }

        $hotelIdsStr = implode(',', array_map('intval', $permittedHotels));
        $hotels = Db::getInstance()->executeS("
            SELECT bi.id, bil.hotel_name, bil.city
            FROM " . _DB_PREFIX_ . "htl_branch_info bi
            JOIN " . _DB_PREFIX_ . "htl_branch_info_lang bil ON bi.id = bil.id AND bil.id_lang = 2
            WHERE bi.active = 1 AND bi.id IN ({$hotelIdsStr})
            ORDER BY bi.id
        ");

        $baseUrl = Tools::getHttpHost(true) . __PS_BASE_URI__;

        // Tạo danh sách đường dẫn xuất iCal cho từng cơ sở
        $exportFeeds = [];
        if ($hotels && is_array($hotels)) {
            foreach ($hotels as $h) {
                $exportFeeds[] = [
                    'id_hotel' => $h['id'],
                    'hotel_name' => $h['hotel_name'],
                    'city' => $h['city'],
                    'export_url' => $baseUrl . 'index.php?fc=module&module=qlovietqr&controller=ical&id_hotel=' . $h['id']
                ];
            }
        }

        // Lấy danh sách các feed đã nhập
        $importedFeeds = Db::getInstance()->executeS("
            SELECT f.*, bil.hotel_name
            FROM " . _DB_PREFIX_ . "ota_sync_feed f
            JOIN " . _DB_PREFIX_ . "htl_branch_info_lang bil ON f.id_hotel = bil.id AND bil.id_lang = 2
            WHERE f.id_hotel IN ({$hotelIdsStr})
            ORDER BY f.id_feed DESC
        ");

        $this->context->smarty->assign([
            'ota_export_feeds' => $exportFeeds,
            'ota_imported_feeds' => $importedFeeds,
            'ota_hotels' => $hotels,
            'current_token' => $this->token,
            'current_index' => self::$currentIndex,
        ]);

        return $this->context->smarty->fetch(
            _PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default'
            . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'controllers'
            . DIRECTORY_SEPARATOR . 'ota_sync' . DIRECTORY_SEPARATOR . 'view.tpl'
        );
    }
}
