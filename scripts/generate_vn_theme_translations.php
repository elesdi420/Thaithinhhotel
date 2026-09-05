<?php
/**
 * Sinh tệp dịch tiếng Việt cho theme (themes/hotel-reservation-theme/lang/vn.php).
 *
 * Theme chỉ có sẵn lang/en.php nên mọi chuỗi trong template rơi về tiếng Anh dù
 * shop chạy tiếng Việt. Khoá dịch của theme là md5 gắn với tên template, và toàn
 * bộ khoá đó đã nằm sẵn trong en.php - nên script này lấy en.php làm khung, giữ
 * nguyên khoá, chỉ thay phần giá trị theo từ điển bên dưới.
 *
 * Chuỗi không có trong từ điển giữ nguyên tiếng Anh, đúng như hành vi trước đây,
 * nên chạy lại script không thể làm hỏng trang nào.
 *
 * Chạy:  docker compose exec web php /var/www/html/scripts/generate_vn_theme_translations.php
 */

$themeLangDir = dirname(__DIR__).'/themes/hotel-reservation-theme/lang';

require $themeLangDir.'/en.php';
$en = $_LANG;

/** Chuỗi gốc => bản dịch. */
$dict = array(
    // Điều hướng, tài khoản
    'Home' => 'Trang chủ',
    'Our Properties' => 'Cơ sở của chúng tôi',
    'Our properties' => 'Cơ sở của chúng tôi',
    'Contact Us' => 'Liên hệ',
    'Contact us' => 'Liên hệ',
    'Sign in' => 'Đăng nhập',
    'Sign out' => 'Đăng xuất',
    'Log in' => 'Đăng nhập',
    'My account' => 'Tài khoản của tôi',
    'My Account' => 'Tài khoản của tôi',

    // Trang danh sách cơ sở
    'View Property' => 'Xem cơ sở',
    'View property' => 'Xem cơ sở',
    'View on map' => 'Xem trên bản đồ',
    'Main Branch' => 'Cơ sở chính',
    'Address' => 'Địa chỉ',

    // Ô tìm phòng và trang phòng
    'Select Hotel' => 'Chọn cơ sở',
    'Check In' => 'Nhận phòng',
    'Check Out' => 'Trả phòng',
    'Check-in' => 'Nhận phòng',
    'Check-out' => 'Trả phòng',
    'Check In - Check Out' => 'Nhận phòng - Trả phòng',
    'Check-in and check-out time' => 'Giờ nhận và trả phòng',
    'Search Rooms' => 'Tìm phòng',
    'Search rooms' => 'Tìm phòng',
    'Adult' => 'Người lớn',
    'Adults' => 'Người lớn',
    'Child' => 'Trẻ em',
    'Children' => 'Trẻ em',
    'All Children' => 'Tất cả trẻ em',
    'Add Room' => 'Thêm phòng',
    'Remove' => 'Xoá',
    '1 Adult, 1 Room' => '1 Người lớn, 1 Phòng',
    'Rooms' => 'Phòng',
    'Done' => 'Xong',
    'Room' => 'Phòng',
    'Rooms' => 'Phòng',
    'Guests' => 'Khách',
    'Book Now' => 'Đặt ngay',
    'Book now' => 'Đặt ngay',
    'Per Night' => 'mỗi đêm',
    'Amenities' => 'Tiện nghi',
    'Description' => 'Mô tả',
    'Price' => 'Giá',
    'Total' => 'Tổng cộng',
    'Quantity' => 'Số lượng',

    // Trang liên hệ
    'Mail Us' => 'Gửi email',
    'Phone' => 'Điện thoại',
    'Email' => 'Email',
    'Send Message' => 'Gửi tin nhắn',
    'Send message' => 'Gửi tin nhắn',
    'Your Name' => 'Họ và tên',
    'Message' => 'Nội dung',
    'Subject' => 'Chủ đề',

    // Chân trang
    'Subscribe' => 'Đăng ký',
    'Newsletter' => 'Bản tin',
    'GET NOTIFICATIONS' => 'NHẬN THÔNG BÁO',
    'Get notifications' => 'Nhận thông báo',
    'FOLLOW US ON' => 'THEO DÕI CHÚNG TÔI',
    'Follow us on' => 'Theo dõi chúng tôi',
    'EXPLORE' => 'KHÁM PHÁ',
    'Explore' => 'Khám phá',
    'payment accepted' => 'phương thức thanh toán',
    'PAYMENT ACCEPTED' => 'PHƯƠNG THỨC THANH TOÁN',
    'Secure Payment' => 'Thanh toán an toàn',
    'About Us' => 'Giới thiệu',
    'About us' => 'Giới thiệu',
    'All rights reserved.' => 'Bảo lưu mọi quyền.',

    // Trang chi tiết phòng
    'Room Information' => 'Thông tin phòng',
    'Max Capacity' => 'Sức chứa tối đa',
    'Additional Facilities' => 'Dịch vụ thêm',
    'Add to cart' => 'Thêm vào giỏ',
    'Modify Search' => 'Sửa tìm kiếm',
    'Searched results for' => 'Kết quả tìm kiếm cho',
    'Searched results for:' => 'Kết quả tìm kiếm cho:',
    'Refund Policies' => 'Chính sách hoàn tiền',
    'Non Refundable' => 'Không hoàn tiền',
    'Reviews' => 'Đánh giá',
    'No reviews.' => 'Chưa có đánh giá.',
    'All Children' => 'Tất cả trẻ em',
    'Back' => 'Quay lại',
    '/Night' => '/đêm',
    'Check-in: ' => 'Nhận phòng: ',
    'Check-out: ' => 'Trả phòng: ',
    'GET NOTIFICATIONS' => 'NHẬN THÔNG BÁO',
    'payment accepted' => 'phương thức thanh toán',
    'Make Booking' => 'Đặt phòng',

    // Giỏ hàng, thanh toán
    'Cart' => 'Giỏ hàng',
    'Your cart' => 'Giỏ hàng của bạn',
    'No products' => 'Chưa có phòng nào',
    'Check out' => 'Thanh toán',
    'Continue browsing' => 'Tiếp tục xem phòng',
    'Proceed to checkout' => 'Tiến hành thanh toán',
    'Order' => 'Đơn hàng',
    'Orders' => 'Đơn hàng',
    'Order history' => 'Lịch sử đơn hàng',
    'Guest Tracking' => 'Tra cứu đơn hàng',
    'Password' => 'Mật khẩu',
    'Email address' => 'Địa chỉ email',
    'Create an account' => 'Tạo tài khoản',
    'Forgot your password?' => 'Quên mật khẩu?',
    'Authentication' => 'Đăng nhập',
    'Already registered?' => 'Đã có tài khoản?',
);

// 1) Giữ nguyên khung en.php, dịch những giá trị có trong từ điển.
$out = array();
$translated = 0;
foreach ($en as $key => $value) {
    if (isset($dict[$value])) {
        $out[$key] = $dict[$value];
        ++$translated;
    } else {
        $out[$key] = $value;
    }
}

/*
 * 2) en.php chỉ chứa những chuỗi từng được công cụ trích xuất của PrestaShop quét
 * qua, nên nhiều chuỗi đang hiển thị lại không có khoá nào trong đó - dịch theo
 * khung en.php thôi là không đủ. Quét thẳng template của theme và tự dựng khoá
 * theo đúng công thức của Translate::getFrontTranslation():
 *
 *     strtolower(tên_tệp_tpl_không_đuôi) . '_' . md5(chuỗi_gốc)
 *
 * Chỉ lấy {l s='...'} không kèm mod= - có mod= là chuỗi của module, thuộc phạm vi
 * scripts/generate_vn_module_translations.php.
 */
$themeDir = dirname($themeLangDir);
$scanned = 0;

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($themeDir, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if ('tpl' !== strtolower($file->getExtension())) {
        continue;
    }
    $path = $file->getPathname();
    // Template ghi đè của module nằm trong theme vẫn là chuỗi của module.
    if (false !== strpos(str_replace('\\', '/', $path), '/modules/')) {
        continue;
    }

    $source = strtolower($file->getBasename('.tpl'));
    $code = file_get_contents($path);
    if (!preg_match_all('/\{l\s+s=\s*(["\'])((?:(?!\1).)*)\1([^}]*)\}/s', $code, $m, PREG_SET_ORDER)) {
        continue;
    }
    foreach ($m as $hit) {
        if (false !== strpos($hit[3], 'mod=')) {
            continue;
        }
        $string = stripslashes($hit[2]);
        if (!isset($dict[$string])) {
            continue;
        }
        $key = $source.'_'.md5($string);
        if (!isset($out[$key]) || $out[$key] !== $dict[$string]) {
            $out[$key] = $dict[$string];
            ++$scanned;
        }
    }
}

$php = "<?php\n\n"
     ."// Bản dịch tiếng Việt cho theme hotel-reservation-theme.\n"
     ."// Sinh bởi scripts/generate_vn_theme_translations.php - sửa từ điển trong script\n"
     ."// đó rồi chạy lại, đừng sửa tay tệp này.\n"
     ."// Chuỗi chưa dịch giữ nguyên tiếng Anh nên không thể gây thoái lui.\n\n"
     ."global \$_LANG;\n\$_LANG = array();\n\n";
foreach ($out as $key => $value) {
    $php .= "\$_LANG['".$key."'] = '".str_replace(array('\\', "'"), array('\\\\', "\\'"), $value)."';\n";
}
file_put_contents($themeLangDir.'/vn.php', $php);

echo 'Tổng khoá ghi ra:            '.count($out)."\n";
echo 'Dịch theo khung en.php:      '.$translated."\n";
echo 'Dịch thêm từ quét template:  '.$scanned."\n";
