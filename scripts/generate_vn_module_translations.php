<?php
/**
 * Sinh tệp dịch tiếng Việt cho các module (translations/vn.php).
 *
 * Bối cảnh: shop chạy mặc định tiếng Việt nhưng không module nào có thư mục
 * translations/, nên mọi chuỗi giao diện rơi về tiếng Anh - thanh điều hướng đọc là
 * "Home / Rooms / Contact Us" trên một website tiếng Việt.
 *
 * Cách hoạt động: quét chuỗi dịch được trong mã module rồi đối chiếu từ điển bên
 * dưới. Khoá tra cứu dựng đúng công thức của Translate::getModuleTranslation():
 *
 *     strtolower('<{' . tên_module . '}prestashop>' . nguồn) . '_' . md5(chuỗi_gốc)
 *
 * trong đó "nguồn" là tên module với chuỗi gọi từ file PHP ($this->l), và là tên
 * tệp .tpl (không đuôi) với chuỗi trong template ({l s='...' mod='...'}).
 *
 * Chuỗi không có trong từ điển được bỏ qua - nó tiếp tục hiển thị tiếng Anh, đúng
 * như trước, nên chạy script này không thể làm hỏng trang nào.
 *
 * Chạy:  docker compose exec web php /var/www/html/scripts/generate_vn_module_translations.php
 */

define('_PS_ADMIN_DIR_', dirname(__DIR__).'/admin2833tvua4');
require_once dirname(__DIR__).'/config/config.inc.php';

$moduleDir = dirname(__DIR__).'/modules/';

/** Module cần việt hoá: hành trình đặt phòng của khách + bảng điều khiển quản trị. */
$targets = array(
    // Hành trình khách hàng
    'blocknavigationmenu', 'blockuserinfo', 'blockcart', 'blocklanguages',
    'wkroomsearchblock', 'wkhotelroom', 'wkabouthotelblock', 'wkhotelfeaturesblock',
    'wkroomtypefeatures', 'blocknewsletter', 'blocksocial', 'hotelreservationsystem',
    'wkfooterpaymentblock', 'wkhotelfilterblock', 'wktestimonialblock', 'qlohotelreview',
    // Bảng điều khiển
    'dashguestcycle', 'dashactivity', 'dashoccupancy', 'dashtrends',
);

/** Chuỗi gốc => bản dịch. Chỉ dịch chuỗi khách và lễ tân thực sự nhìn thấy. */
$dict = array(
    // --- Điều hướng, tài khoản, giỏ hàng ---
    'Home' => 'Trang chủ',
    'Our Properties' => 'Cơ sở của chúng tôi',
    'Contact Us' => 'Liên hệ',
    'Contact us' => 'Liên hệ',
    'Sign in' => 'Đăng nhập',
    'Sign out' => 'Đăng xuất',
    'Log in' => 'Đăng nhập',
    'My account' => 'Tài khoản của tôi',
    'My Account' => 'Tài khoản của tôi',
    'Welcome' => 'Xin chào',
    'Cart' => 'Giỏ hàng',
    'Your cart' => 'Giỏ hàng của bạn',
    'No products' => 'Chưa có phòng nào',
    'Total' => 'Tổng cộng',
    'Tax' => 'Thuế',
    'Check out' => 'Thanh toán',
    'Checkout' => 'Thanh toán',
    'Continue browsing' => 'Tiếp tục xem phòng',
    'Proceed to checkout' => 'Tiến hành thanh toán',
    'Prices are tax included' => 'Giá đã bao gồm thuế',
    'Product successfully added to your cart' => 'Đã thêm vào giỏ hàng',
    'Room successfully added to your cart' => 'Đã thêm phòng vào giỏ hàng',
    'Quantity' => 'Số lượng',
    'Unit Price' => 'Đơn giá',
    'Hotel Name' => 'Cơ sở',
    'Room type cost' => 'Tiền phòng',
    'Language' => 'Ngôn ngữ',

    // --- Ô tìm phòng ---
    'Select Hotel' => 'Chọn cơ sở',
    'Check-in' => 'Nhận phòng',
    'Check-out' => 'Trả phòng',
    'Check In' => 'Nhận phòng',
    'Check Out' => 'Trả phòng',
    'Search Rooms' => 'Tìm phòng',
    'Search rooms' => 'Tìm phòng',
    'Search' => 'Tìm kiếm',
    'Adult' => 'Người lớn',
    'Adults' => 'Người lớn',
    'Child' => 'Trẻ em',
    'Children' => 'Trẻ em',
    'Room' => 'Phòng',
    'Rooms' => 'Phòng',
    'Guests' => 'Khách',
    'Add Room' => 'Thêm phòng',
    'Remove' => 'Xoá',
    '1 Adult, 1 Room' => '1 Người lớn, 1 Phòng',
    'Done' => 'Xong',
    'Occupancy' => 'Số khách',

    // --- Thẻ phòng, trang phòng ---
    'Book Now' => 'Đặt ngay',
    'Book now' => 'Đặt ngay',
    'book now' => 'Đặt ngay',
    'Per Night' => 'mỗi đêm',
    'per night' => 'mỗi đêm',
    'Read More' => 'Xem thêm',
    'Read Less' => 'Thu gọn',
    'Read More.' => 'Xem thêm.',
    'Read Less.' => 'Thu gọn.',
    'Total Rooms Cost in cart' => 'Tiền phòng trong giỏ',
    'Total Product Cost in cart' => 'Tiền dịch vụ trong giỏ',
    'Convenience Fees' => 'Phí tiện ích',
    '(tax incl.)' => '(đã gồm thuế)',
    'There are [1]%d[/1] item(s) in your cart.' => 'Giỏ hàng có [1]%d[/1] mục.',
    '1 item in your cart.' => 'Giỏ hàng có 1 mục.',
    'Time Duration' => 'Thời gian lưu trú',
    'Room occupancy' => 'Số khách',
    'View on map' => 'Xem trên bản đồ',
    'View Details' => 'Xem chi tiết',
    'View details' => 'Xem chi tiết',
    'Amenities' => 'Tiện nghi',
    // Bộ lọc phòng ở cột trái trang danh sách (wkhotelfilterblock).
    'Clear Filter' => 'Xoá lọc',
    'View More' => 'Xem thêm',
    'Book Now' => 'Đặt ngay',
    // Thanh tóm tắt tìm kiếm trên trang phòng (wkroomsearchblock).
    'Searched results for' => 'Kết quả tìm kiếm cho',
    'Modify Search' => 'Sửa tìm kiếm',
    'Days' => 'Ngày',
    'Nights' => 'Đêm',
    'Night' => 'Đêm',
    // Đánh giá phòng (qlohotelreview).
    'Review' => 'Đánh giá',
    'Review(s)' => 'Đánh giá',
    'Reviews' => 'Đánh giá',
    'Description' => 'Mô tả',
    'Price' => 'Giá',
    'Available' => 'Còn trống',
    'Not Available' => 'Đã kín',

    // --- Khối tiêu đề, tìm kiếm, đánh giá ---
    'Welcome To' => 'Chào mừng đến với',
    'Searched results for' => 'Kết quả tìm kiếm cho',
    'Searched results for:' => 'Kết quả tìm kiếm cho:',
    'Reviews' => 'Đánh giá',
    'No reviews.' => 'Chưa có đánh giá.',
    'Write a review' => 'Viết đánh giá',
    'Rating' => 'Điểm đánh giá',

    // --- Chân trang ---
    'GET NOTIFICATIONS' => 'NHẬN THÔNG BÁO',
    'Your email address' => 'Địa chỉ email của bạn',
    'No internet. Please try later.' => 'Không có kết nối mạng. Vui lòng thử lại sau.',
    'payment accepted' => 'phương thức thanh toán',
    'Make Booking' => 'Đặt phòng',
    'Room Information' => 'Thông tin phòng',
    'Max Capacity' => 'Sức chứa tối đa',
    'Modify Search' => 'Sửa tìm kiếm',
    'All Children' => 'Tất cả trẻ em',
    'Reviews' => 'Đánh giá',
    'No reviews.' => 'Chưa có đánh giá.',
    'Subscribe' => 'Đăng ký',
    'Newsletter' => 'Bản tin',
    'Follow us on' => 'Theo dõi chúng tôi',
    'Explore' => 'Khám phá',
    'Secure Payment' => 'Thanh toán an toàn',
    'About Us' => 'Giới thiệu',
    'About us' => 'Giới thiệu',
    'All rights reserved.' => 'Bảo lưu mọi quyền.',
    'Bảo lưu mọi quyền.' => 'Bảo lưu mọi quyền.',

    // --- Bảng điều khiển: thẻ chỉ số ---
    'Arrivals' => 'Khách đến',
    'Departures' => 'Khách đi',
    'New Bookings' => 'Đơn mới',
    'Occupied Rooms' => 'Phòng có khách',
    'Guest Messages' => 'Tin nhắn khách',
    'Cancelled Bookings' => 'Đơn đã huỷ',
    'Guests (Adults/Children)' => 'Khách (Người lớn/Trẻ em)',

    // --- Bảng điều khiển: chú thích khi rê chuột ---
    'The number of arrivals scheduled for today.' => 'Số lượt khách dự kiến nhận phòng hôm nay.',
    'The number of departures scheduled for today.' => 'Số lượt khách dự kiến trả phòng hôm nay.',
    'The number of new bookings created today so far.' => 'Số đơn đặt phòng mới tạo trong hôm nay.',
    'The count of rooms currently occupied by guests.' => 'Số phòng hiện đang có khách lưu trú.',
    'The number of new messages received from guests today.' => 'Số tin nhắn mới nhận từ khách trong hôm nay.',
    'The number of bookings cancelled today so far.' => 'Số đơn đặt phòng bị huỷ trong hôm nay.',
    'The number of adults and children scheduled to stay today.' => 'Số người lớn và trẻ em dự kiến lưu trú hôm nay.',

    // --- Bảng điều khiển: khối hoạt động ---
    'Operations Today' => 'Hoạt động hôm nay',
    'In-house' => 'Đang lưu trú',
    'Cancellations' => 'Đã huỷ',
    'Upcoming Arrivals' => 'Sắp nhận phòng',
    'Upcoming Departures' => 'Sắp trả phòng',
    'Activity' => 'Hoạt động',
    'Occupancy' => 'Công suất phòng',
    'Revenue' => 'Doanh thu',
    'Sales' => 'Doanh thu',
    'Orders' => 'Đơn hàng',
    'Customers' => 'Khách hàng',
    'New customers' => 'Khách hàng mới',
    'Pending orders' => 'Đơn chờ xử lý',
    'Visits' => 'Lượt truy cập',
    'Unique visitors' => 'Khách truy cập',
    'Online visitors' => 'Đang truy cập',
    'Abandoned carts' => 'Giỏ hàng bỏ dở',
    'Refresh' => 'Làm mới',
    'Available' => 'Còn trống',
    'Unavailable' => 'Khoá phòng',
    'Occupied' => 'Có khách',
    'Customer Name' => 'Tên khách',
    'Room No.' => 'Số phòng',
    'Room Type' => 'Hạng phòng',
    'Hotel' => 'Cơ sở',
    'Order ID' => 'Mã đơn',
    'No result' => 'Chưa có dữ liệu',
);

/**
 * Trích chuỗi dịch được của một module.
 *
 * @param string $dir Thư mục module
 * @param string $name Tên module
 * @return array<string,string[]> chuỗi gốc => danh sách "nguồn"
 */
function collectStrings($dir, $name)
{
    $found = array();

    $addHit = function ($string, $source) use (&$found) {
        $string = stripslashes($string);
        if (!isset($found[$string])) {
            $found[$string] = array();
        }
        if (!in_array($source, $found[$string])) {
            $found[$string][] = $source;
        }
    };

    /*
     * Quét cả thư mục module lẫn bản ghi đè template nằm trong theme
     * (themes/<theme>/modules/<module>/) - nhiều chuỗi khách nhìn thấy nằm ở bản
     * ghi đè chứ không nằm trong module gốc.
     */
    $roots = array($dir);
    $override = dirname(__DIR__).'/themes/hotel-reservation-theme/modules/'.$name;
    if (is_dir($override)) {
        $roots[] = $override;
    }

    $files = array();
    foreach ($roots as $root) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $f) {
            $files[] = $f;
        }
    }

    foreach ($files as $file) {
        $path = $file->getPathname();
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, array('php', 'tpl'))) {
            continue;
        }
        if (false !== strpos($path, DIRECTORY_SEPARATOR.'translations'.DIRECTORY_SEPARATOR)) {
            continue;
        }
        $code = file_get_contents($path);

        if ('php' === $ext) {
            // $this->l('...') - nguồn là tên module
            if (preg_match_all('/->l\(\s*(["\'])((?:(?!\1).)*)\1/s', $code, $m)) {
                foreach ($m[2] as $s) {
                    $addHit($s, $name);
                }
            }
        } else {
            // {l s='...' mod='name'} - nguồn là tên tệp tpl không đuôi
            $source = strtolower(basename($path, '.tpl'));
            if (preg_match_all('/\{l\s+s=\s*(["\'])((?:(?!\1).)*)\1/s', $code, $m)) {
                foreach ($m[2] as $s) {
                    $addHit($s, $source);
                }
            }
        }
    }

    return $found;
}

$totalKeys = 0;
$report = array();

foreach ($targets as $name) {
    $dir = $moduleDir.$name;
    if (!is_dir($dir)) {
        $report[] = sprintf('%-24s (không có thư mục, bỏ qua)', $name);
        continue;
    }

    $strings = collectStrings($dir, $name);
    $lines = array();
    $translated = 0;

    foreach ($strings as $string => $sources) {
        if (!isset($dict[$string])) {
            continue;
        }
        ++$translated;
        foreach ($sources as $source) {
            $key = strtolower('<{'.$name.'}prestashop>'.$source).'_'.md5($string);
            $lines[$key] = $dict[$string];
        }
    }

    if (!$lines) {
        $report[] = sprintf('%-24s %3d chuỗi, không có chuỗi nào cần dịch', $name, count($strings));
        continue;
    }

    if (!is_dir($dir.'/translations')) {
        mkdir($dir.'/translations', 0755, true);
    }

    $php = "<?php\n\n"
         ."// Bản dịch tiếng Việt cho module ".$name.".\n"
         ."// Sinh bởi scripts/generate_vn_module_translations.php - sửa từ điển trong\n"
         ."// script đó rồi chạy lại, đừng sửa tay tệp này.\n"
         ."// Chuỗi chưa dịch không xuất hiện ở đây và tiếp tục hiển thị tiếng Anh.\n\n"
         ."global \$_MODULE;\n\$_MODULE = array();\n\n";
    foreach ($lines as $key => $value) {
        $php .= "\$_MODULE['".$key."'] = '".str_replace(array('\\', "'"), array('\\\\', "\\'"), $value)."';\n";
    }
    file_put_contents($dir.'/translations/vn.php', $php);

    // index.php giữ nguyên nếp của dự án: chặn liệt kê thư mục
    if (!file_exists($dir.'/translations/index.php') && file_exists($moduleDir.'index.php')) {
        copy($moduleDir.'index.php', $dir.'/translations/index.php');
    }

    $totalKeys += count($lines);
    $report[] = sprintf('%-24s %3d chuỗi quét được, dịch %2d, ghi %3d khoá', $name, count($strings), $translated, count($lines));
}

echo implode("\n", $report)."\n\n";
echo 'Tổng số khoá đã ghi: '.$totalKeys."\n";
