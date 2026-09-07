<?php
/**
 * QloVietQrIcalModuleFrontController
 * Cung cấp endpoint xuất lịch phòng chuẩn iCalendar (.ics) cho các nền tảng OTA (Airbnb, Agoda, Booking.com)
 */

require_once _PS_ROOT_DIR_ . '/classes/OtaCalendarSync.php';

class QloVietQrIcalModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ajax = true;

    public function initContent()
    {
        parent::initContent();

        $idHotel = (int) Tools::getValue('id_hotel', 1);
        $idRoomType = (int) Tools::getValue('id_room_type', 0);

        if ($idHotel <= 0) {
            header('HTTP/1.1 404 Not Found');
            die('Chi nhánh không tồn tại');
        }

        $icsContent = OtaCalendarSyncCore::generateIcal($idHotel, $idRoomType);

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename="hotel-' . $idHotel . '-calendar.ics"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $icsContent;
        exit;
    }
}
