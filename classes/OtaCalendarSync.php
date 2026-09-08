<?php
/**
 * OtaCalendarSync - Quản lý Đồng bộ Lịch 2 Chiều với OTA (Airbnb, Agoda, Booking.com)
 * Chuẩn công nghiệp iCalendar (RFC 5545)
 */

class OtaCalendarSyncCore
{
    /**
     * Xuất chuỗi nội dung file .ics từ danh sách booking và ngày khoá của khách sạn
     *
     * @param int $idHotel ID chi nhánh
     * @param int $idRoomType ID hạng phòng (tuỳ chọn, 0 = tất cả)
     * @return string Chuỗi iCalendar hợp lệ
     */
    public static function generateIcal($idHotel, $idRoomType = 0)
    {
        $idHotel = (int) $idHotel;
        $idRoomType = (int) $idRoomType;

        $hotelName = Db::getInstance()->getValue(
            "SELECT hotel_name FROM " . _DB_PREFIX_ . "htl_branch_info_lang WHERE id = {$idHotel} AND id_lang = 2"
        );
        if (!$hotelName) {
            $hotelName = "CMS Hotel Branch #{$idHotel}";
        }

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//CMS Hotel//PMS OTA Sync v1.0//VN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . self::escapeIcalText($hotelName . ' - Lịch Đặt Phòng'),
            'X-WR-TIMEZONE:Asia/Ho_Chi_Minh'
        ];

        // 1. Đọc các đơn đặt phòng hợp lệ từ qlo_htl_booking_detail
        $sql = "SELECT bd.id, bd.id_order, bd.id_room, bd.date_from, bd.date_to, bd.room_num, bd.room_type_name
                FROM " . _DB_PREFIX_ . "htl_booking_detail bd
                WHERE bd.id_hotel = {$idHotel}
                  AND bd.is_cancelled = 0
                  AND bd.is_refunded = 0
                  AND bd.date_to >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        
        if ($idRoomType > 0) {
            $sql .= " AND bd.id_product = {$idRoomType}";
        }

        $bookings = Db::getInstance()->executeS($sql);
        if ($bookings && is_array($bookings)) {
            foreach ($bookings as $b) {
                $dtStart = date('Ymd', strtotime($b['date_from']));
                $dtEnd = date('Ymd', strtotime($b['date_to']));
                $uid = "booking-{$b['id']}@cmshotel.local";
                $summary = "Đã đặt (Reserved) - Phòng " . ($b['room_num'] ?: 'Standard');
                $desc = "Đơn đặt phòng #{$b['id_order']} - {$b['room_type_name']}";

                $lines[] = 'BEGIN:VEVENT';
                $lines[] = 'UID:' . $uid;
                $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
                $lines[] = 'DTSTART;VALUE=DATE:' . $dtStart;
                $lines[] = 'DTEND;VALUE=DATE:' . $dtEnd;
                $lines[] = 'SUMMARY:' . self::escapeIcalText($summary);
                $lines[] = 'DESCRIPTION:' . self::escapeIcalText($desc);
                $lines[] = 'STATUS:CONFIRMED';
                $lines[] = 'END:VEVENT';
            }
        }

        // 2. Đọc các khoảng ngày đã khóa thủ công hoặc từ kênh khác trong qlo_htl_room_disable_dates
        $sqlDis = "SELECT dd.id, dd.id_room_type, dd.id_room, dd.date_from, dd.date_to, dd.reason
                   FROM " . _DB_PREFIX_ . "htl_room_disable_dates dd
                   JOIN " . _DB_PREFIX_ . "htl_room_information ri ON dd.id_room = ri.id
                   WHERE ri.id_hotel = {$idHotel}
                     AND dd.date_to >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        
        if ($idRoomType > 0) {
            $sqlDis .= " AND dd.id_room_type = {$idRoomType}";
        }

        $disabled = Db::getInstance()->executeS($sqlDis);
        if ($disabled && is_array($disabled)) {
            foreach ($disabled as $d) {
                $dtStart = date('Ymd', strtotime($d['date_from']));
                $dtEnd = date('Ymd', strtotime($d['date_to']));
                $uid = "block-{$d['id']}@cmshotel.local";
                $summary = "Phòng đã khóa (Blocked)";
                $desc = $d['reason'] ?: 'Khóa phòng nội bộ';

                $lines[] = 'BEGIN:VEVENT';
                $lines[] = 'UID:' . $uid;
                $lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');
                $lines[] = 'DTSTART;VALUE=DATE:' . $dtStart;
                $lines[] = 'DTEND;VALUE=DATE:' . $dtEnd;
                $lines[] = 'SUMMARY:' . self::escapeIcalText($summary);
                $lines[] = 'DESCRIPTION:' . self::escapeIcalText($desc);
                $lines[] = 'STATUS:CONFIRMED';
                $lines[] = 'END:VEVENT';
            }
        }

        $lines[] = 'END:VCALENDAR';
        $lines[] = '';

        return implode("\r\n", $lines);
    }

    /**
     * Phân tích nội dung iCalendar (.ics) thô thành mảng các sự kiện VEVENT
     */
    public static function parseIcalContent($content)
    {
        // Chuẩn hóa xuống dòng và nối các dòng folded (bắt đầu bằng khoảng trắng hoặc tab)
        $content = preg_replace('/\r\n[ \t]|\r[ \t]|\n[ \t]/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', $content);

        $events = [];
        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
                continue;
            }

            if ($line === 'END:VEVENT') {
                if ($currentEvent && isset($currentEvent['DTSTART']) && isset($currentEvent['DTEND'])) {
                    $events[] = $currentEvent;
                }
                $currentEvent = null;
                continue;
            }

            if ($currentEvent !== null) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $keyPart = strtoupper($parts[0]);
                    $val = $parts[1];

                    // Tách tên thuộc tính nếu có tham số (ví dụ DTSTART;VALUE=DATE)
                    $keySubParts = explode(';', $keyPart);
                    $propName = $keySubParts[0];

                    switch ($propName) {
                        case 'UID':
                            $currentEvent['UID'] = $val;
                            break;
                        case 'DTSTART':
                            $currentEvent['DTSTART'] = self::parseIcalDate($val);
                            break;
                        case 'DTEND':
                            $currentEvent['DTEND'] = self::parseIcalDate($val);
                            break;
                        case 'SUMMARY':
                            $currentEvent['SUMMARY'] = self::unescapeIcalText($val);
                            break;
                        case 'DESCRIPTION':
                            $currentEvent['DESCRIPTION'] = self::unescapeIcalText($val);
                            break;
                    }
                }
            }
        }

        return $events;
    }

    /**
     * Đồng bộ một feed OTA cụ thể
     */
    public static function syncFeed($idFeed)
    {
        $idFeed = (int) $idFeed;
        $feed = Db::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "ota_sync_feed WHERE id_feed = {$idFeed}");
        if (!$feed) {
            return ['success' => false, 'message' => 'Feed không tồn tại'];
        }

        $url = trim($feed['feed_url']);
        if (empty($url)) {
            return ['success' => false, 'message' => 'Đường dẫn feed trống'];
        }

        // Tải nội dung file .ics từ OTA URL
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'user_agent' => 'Mozilla/5.0 (CMS Hotel PMS Calendar Sync)'
            ]
        ]);
        $icsContent = @file_get_contents($url, false, $context);
        if ($icsContent === false) {
            $err = "Không thể kết nối tải feed từ: {$url}";
            Db::getInstance()->execute("
                UPDATE " . _DB_PREFIX_ . "ota_sync_feed 
                SET last_sync_at = NOW(), last_sync_status = '" . pSQL($err) . "'
                WHERE id_feed = {$idFeed}
            ");
            return ['success' => false, 'message' => $err];
        }

        $events = self::parseIcalContent($icsContent);
        $idHotel = (int) $feed['id_hotel'];
        $idRoomType = (int) $feed['id_room_type'];
        $channel = pSQL($feed['channel_name']);

        // Tìm một phòng khả dụng trong cơ sở để gán khoá ngày
        $whereProduct = ($idRoomType > 0) ? " AND id_product = " . (int) $idRoomType : "";
        $roomInfo = Db::getInstance()->getRow("
            SELECT id, id_product FROM " . _DB_PREFIX_ . "htl_room_information 
            WHERE id_hotel = {$idHotel}{$whereProduct}
        ");

        if (!$roomInfo) {
            $err = "Không tìm thấy phòng tương ứng cho chi nhánh #{$idHotel}";
            Db::getInstance()->execute("
                UPDATE " . _DB_PREFIX_ . "ota_sync_feed 
                SET last_sync_at = NOW(), last_sync_status = '" . pSQL($err) . "'
                WHERE id_feed = {$idFeed}
            ");
            return ['success' => false, 'message' => $err];
        }

        $targetRoomId = (int) $roomInfo['id'];
        $targetRoomType = (int) $roomInfo['id_product'];

        // Xóa các block cũ của feed này để cập nhật mới nhất (idempotent)
        $tagPrefix = "OTA-FEED-{$idFeed}:";
        Db::getInstance()->execute("
            DELETE FROM " . _DB_PREFIX_ . "htl_room_disable_dates 
            WHERE reason LIKE '{$tagPrefix}%'
        ");

        $syncedCount = 0;
        foreach ($events as $ev) {
            $dateFrom = $ev['DTSTART'];
            $dateTo = $ev['DTEND'];
            $uid = isset($ev['UID']) ? pSQL($ev['UID']) : uniqid();
            $summary = isset($ev['SUMMARY']) ? pSQL($ev['SUMMARY']) : 'OTA Booking';

            if ($dateFrom && $dateTo && $dateFrom < $dateTo) {
                $reason = "{$tagPrefix} {$channel} [{$uid}] - {$summary}";
                Db::getInstance()->execute("
                    INSERT INTO " . _DB_PREFIX_ . "htl_room_disable_dates 
                    (`id_room_type`, `id_room`, `date_from`, `date_to`, `reason`, `date_add`, `date_upd`)
                    VALUES ({$targetRoomType}, {$targetRoomId}, '{$dateFrom}', '{$dateTo}', '{$reason}', NOW(), NOW())
                ");
                $syncedCount++;
            }
        }

        if ($syncedCount > 0) {
            Db::getInstance()->execute("
                UPDATE " . _DB_PREFIX_ . "htl_room_information 
                SET id_status = " . (int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE . "
                WHERE id = {$targetRoomId} AND id_status != " . (int) HotelRoomInformation::STATUS_INACTIVE . "
            ");
        } else {
            $otherBlocks = (int) Db::getInstance()->getValue("
                SELECT COUNT(*) FROM " . _DB_PREFIX_ . "htl_room_disable_dates WHERE id_room = {$targetRoomId}
            ");
            if ($otherBlocks === 0) {
                Db::getInstance()->execute("
                    UPDATE " . _DB_PREFIX_ . "htl_room_information 
                    SET id_status = " . (int) HotelRoomInformation::STATUS_ACTIVE . "
                    WHERE id = {$targetRoomId} AND id_status = " . (int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE . "
                ");
            }
        }

        $statusMsg = "Thành công: Đã đồng bộ {$syncedCount} khoảng ngày khoá phòng";
        Db::getInstance()->execute("
            UPDATE " . _DB_PREFIX_ . "ota_sync_feed 
            SET last_sync_at = NOW(), last_sync_status = '" . pSQL($statusMsg) . "', last_sync_count = {$syncedCount}
            WHERE id_feed = {$idFeed}
        ");

        return [
            'success' => true,
            'message' => $statusMsg,
            'count' => $syncedCount
        ];
    }

    /**
     * Xóa một feed và phục hồi trạng thái phòng nếu không còn ngày khóa nào khác
     */
    public static function deleteFeed($idFeed)
    {
        $idFeed = (int) $idFeed;
        $tagPrefix = "OTA-FEED-{$idFeed}:";

        $affectedRooms = Db::getInstance()->executeS("
            SELECT DISTINCT id_room FROM " . _DB_PREFIX_ . "htl_room_disable_dates WHERE reason LIKE '{$tagPrefix}%'
        ");

        Db::getInstance()->execute("DELETE FROM " . _DB_PREFIX_ . "htl_room_disable_dates WHERE reason LIKE '{$tagPrefix}%'");
        Db::getInstance()->execute("DELETE FROM " . _DB_PREFIX_ . "ota_sync_feed WHERE id_feed = {$idFeed}");

        if ($affectedRooms && is_array($affectedRooms)) {
            foreach ($affectedRooms as $r) {
                $rId = (int) $r['id_room'];
                $rem = (int) Db::getInstance()->getValue("SELECT COUNT(*) FROM " . _DB_PREFIX_ . "htl_room_disable_dates WHERE id_room = {$rId}");
                if ($rem === 0) {
                    Db::getInstance()->execute("
                        UPDATE " . _DB_PREFIX_ . "htl_room_information 
                        SET id_status = " . (int) HotelRoomInformation::STATUS_ACTIVE . " 
                        WHERE id = {$rId} AND id_status = " . (int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE . "
                    ");
                }
            }
        }

        return true;
    }

    /**
     * Đồng bộ toàn bộ các feed đang kích hoạt
     */
    public static function syncAllActiveFeeds()
    {
        $feeds = Db::getInstance()->executeS("SELECT id_feed FROM " . _DB_PREFIX_ . "ota_sync_feed WHERE active = 1");
        $results = [];
        if ($feeds && is_array($feeds)) {
            foreach ($feeds as $f) {
                $results[$f['id_feed']] = self::syncFeed($f['id_feed']);
            }
        }
        return $results;
    }

    private static function parseIcalDate($str)
    {
        $clean = preg_replace('/[^0-9]/', '', $str);
        if (strlen($clean) >= 8) {
            $y = substr($clean, 0, 4);
            $m = substr($clean, 4, 2);
            $d = substr($clean, 6, 2);
            return "{$y}-{$m}-{$d}";
        }
        return null;
    }

    private static function escapeIcalText($text)
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace("\n", '\n', $text);
        return $text;
    }

    private static function unescapeIcalText($text)
    {
        $text = str_replace('\n', "\n", $text);
        $text = str_replace('\,', ',', $text);
        $text = str_replace('\;', ';', $text);
        $text = str_replace('\\\\', '\\', $text);
        return $text;
    }
}
