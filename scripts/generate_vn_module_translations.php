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
    // Báo cáo Thống kê: quản lý chuỗi xem doanh thu ở đây. Tên từng báo cáo
    // trong danh sách bên trái chính là displayName của module, nên phải quét
    // cả nhóm này thì menu đó mới ra tiếng Việt.
    'statsforecast', 'statsdata', 'statsbestcustomers', 'statsbestproducts',
    'statsbestcategories', 'statsbestvouchers', 'statscatalog', 'statscheckup',
    'statsequipment', 'statslive', 'statsnewsletter', 'statsorigin',
    'statspersonalinfos', 'statsproduct', 'statsregistrations', 'statssales',
    'statsvisits', 'sekeywords', 'pagesnotfound', 'qlostatsserviceproducts',
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

    // --- Màn hình Lịch phòng & Đặt phòng (Book Now) - lễ tân dùng hằng ngày ---
    'Book Now' => 'Đặt phòng',
    'Booking Form' => 'Phiếu đặt phòng',
    // Bản gốc viết sai chính tả "Calender"; giữ nguyên khoá, sửa ở bản dịch.
    'Booking Calender' => 'Lịch đặt phòng',
    'Check-In' => 'Nhận phòng',
    'Check-Out' => 'Trả phòng',
    'All Types' => 'Tất cả hạng phòng',
    'Search' => 'Tìm kiếm',
    'Total Rooms' => 'Tổng số phòng',
    'Available Rooms' => 'Phòng còn trống',
    'Total Available' => 'Tổng phòng trống',
    'Partially Available' => 'Trống một phần',
    'Partially Available Rooms' => 'Phòng trống một phần',
    '%s Partially Available Rooms' => '%s trống một phần',
    // Nhãn này nằm trong ô một ngày của lịch nên rất hẹp: bản dài "%s phòng còn
    // trống" bị cắt thành "11 phòng cò...". Rút cho vừa.
    '%s Available Rooms' => '%s phòng trống',
    '%s Booked Rooms' => '%s đã đặt',
    '%s Unavailable Rooms' => '%s bị khoá',
    'Booked Rooms' => 'Phòng đã đặt',
    'Unavailable Rooms' => 'Phòng bị khoá',
    // Ô thống kê rất hẹp: bản dài "Phòng đang giữ trong giỏ" bị cắt còn "Giỏ".
    'In-Cart Rooms' => 'Phòng trong giỏ',
    'Room Occupancy' => 'Sức chứa phòng',
    'Maximum adults' => 'Tối đa người lớn',
    'Maximum children' => 'Tối đa trẻ em',
    'Maximum guests' => 'Tối đa khách',
    // Thông báo JS bật lên khi lễ tân chọn quá sức chứa cho phép.
    'Maximum room occupancy reached' => 'Đã đạt sức chứa tối đa của phòng',
    'Maximum adult occupancy reached' => 'Đã đạt số người lớn tối đa',
    'Maximum children occupancy reached' => 'Đã đạt số trẻ em tối đa',

    // --- Báo cáo Thống kê: tên từng báo cáo ở danh sách bên trái ---
    'Stats Dashboard' => 'Tổng quan Thống kê',
    'Sales and orders' => 'Doanh thu và đơn hàng',
    'Best hotels' => 'Cơ sở doanh thu cao nhất',
    'Best customers' => 'Khách hàng chi nhiều nhất',
    'Best vouchers' => 'Phiếu giảm giá hiệu quả nhất',
    'Best-selling room types' => 'Loại phòng bán chạy nhất',
    'Room type details' => 'Chi tiết loại phòng',
    'Hotel statistics' => 'Thống kê theo cơ sở',
    'Catalog evaluation' => 'Đánh giá danh mục',
    'Customer accounts' => 'Tài khoản khách hàng',
    'Registered customer information' => 'Thông tin khách đã đăng ký',
    'Extra services overview' => 'Tổng quan dịch vụ thêm',
    'Visits and visitors' => 'Lượt truy cập',
    'Visitors online' => 'Khách đang online',
    'Visitors origin' => 'Nguồn truy cập',
    'Browsers and operating systems' => 'Trình duyệt và hệ điều hành',
    'Search engine keywords' => 'Từ khoá tìm kiếm',
    'Pages not found' => 'Trang không tìm thấy',
    'Data mining for statistics' => 'Thu thập dữ liệu thống kê',

    // --- Bảng Tổng quan Thống kê (statsforecast) ---
    'The listed amounts do not include tax.' => 'Các số tiền dưới đây chưa gồm thuế.',
    'Time frame' => 'Khoảng thời gian',
    'Daily' => 'Theo ngày',
    'Weekly' => 'Theo tuần',
    'Monthly' => 'Theo tháng',
    'Yearly' => 'Theo năm',
    'Visits' => 'Lượt truy cập',
    'Registrations' => 'Lượt đăng ký',
    'Orders placed' => 'Đơn đã đặt',
    'Booked rooms' => 'Phòng đã đặt',
    'Percentage of registrations' => 'Tỷ lệ đăng ký',
    'Conversion rate' => 'Tỷ lệ chuyển đổi',
    'Revenue' => 'Doanh thu',
    'Revenue (converted)' => 'Doanh thu (đã quy đổi)',
    'Margin' => 'Lợi nhuận gộp',
    'Total' => 'Tổng cộng',
    'Average' => 'Trung bình',
    'Forecast' => 'Dự báo',
    'Conversion' => 'Chuyển đổi',
    'Visitors' => 'Khách truy cập',
    'Registered' => 'Đã đăng ký',
    'Unregistered' => 'Chưa đăng ký',
    'A simple statistical calculation lets you know the monetary value of your visitors:'
        => 'Một phép tính đơn giản cho biết giá trị bằng tiền của mỗi khách truy cập:',
    'On average, each visitor places an order for this amount:'
        => 'Trung bình mỗi khách truy cập đặt đơn trị giá:',
    'On average, each registered visitor places an order for this amount:'
        => 'Trung bình mỗi khách đã đăng ký đặt đơn trị giá:',
    'Payment distribution' => 'Phân bổ theo phương thức thanh toán',
    'The amounts in this section include taxes, so you can get an estimation of the commission due to the payment method.'
        => 'Số tiền ở mục này đã gồm thuế, để ước lượng phí phải trả cho từng phương thức thanh toán.',
    'Language distribution' => 'Phân bổ theo ngôn ngữ',
    'Zone distribution' => 'Phân bổ theo khu vực',
    'Currency distribution' => 'Phân bổ theo đơn vị tiền tệ',
    'Percentage of orders' => 'Tỷ lệ đơn hàng',
    'Percentage of revenue' => 'Tỷ lệ doanh thu',
    'Average payment value' => 'Giá trị thanh toán trung bình',
    'No data available.' => 'Chưa có dữ liệu.',
    'Zone' => 'Khu vực',
    '-- No filter --' => '-- Không lọc --',
    'Allotment Type' => 'Kiểu xếp phòng',
    'Auto Allotment' => 'Xếp tự động',
    'Manual Allotment' => 'Xếp thủ công',
    'Auto' => 'Tự động',
    'Manual' => 'Thủ công',
    'Select occupancy' => 'Chọn số khách',
    'Select age' => 'Chọn tuổi',
    'Add To Cart' => 'Thêm vào giỏ',
    'Add Room' => 'Thêm phòng',
    'Cart' => 'Giỏ hàng',
    'Cart Options' => 'Tuỳ chọn giỏ hàng',
    'Reallocate' => 'Chuyển phòng',
    'Duration' => 'Thời lượng',
    'Guests' => 'Số khách',
    'Hotel Name' => 'Tên cơ sở',
    'Status' => 'Trạng thái',
    'Action' => 'Thao tác',
    'Close' => 'Đóng',
    'Remove' => 'Bỏ',
    'Service Products' => 'Dịch vụ kèm theo',
    'Rooms Amount (Tax excl.):' => 'Tiền phòng (chưa thuế):',
    'Convenience Fee (Tax excl.):' => 'Phí tiện ích (chưa thuế):',
    'Total Amount (Tax excl.):' => 'Tổng cộng (chưa thuế):',
    'Amount (Tax excl.)' => 'Số tiền (chưa thuế)',
    '1 Adult, 1 Room' => '1 Người lớn, 1 Phòng',
    'Room - 1' => 'Phòng 1',
    'All Children' => 'Tất cả trẻ em',
    'Under 1' => 'Dưới 1',
    'Below' => 'Dưới',
    'years' => 'tuổi',
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
            // Nguồn khoá của $this->l() KHÔNG phải lúc nào cũng là tên module.
            //
            // Với controller admin của module, AdminController::l() gọi
            // Translate::getAdminTranslation($string, get_class($this)); hàm này
            // thấy lớp thuộc một tab của module nên chuyển sang
            // getModuleTranslation(..., $source = get_class($this).'controller'),
            // tức nguồn là TÊN LỚP CONTROLLER viết thường. Trước đây script luôn
            // dùng tên module nên mọi chuỗi trong controller admin của module
            // không bao giờ khớp - "Book Now" và "All Types" trên màn hình Lịch
            // phòng vẫn tiếng Anh dù từ điển đã có.
            //
            // Sinh cả hai nguồn cho chắc: thừa một khoá không hại gì, thiếu thì
            // chuỗi rơi về tiếng Anh.
            $sources = array($name);
            if (preg_match('#'.preg_quote(DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR, '#').'#', $path)) {
                // AdminController::l() cắt 10 ký tự cuối ("Controller") khỏi
                // get_class($this), rồi Translate::getAdminTranslation nối lại
                // chuỗi 'controller'. Kết quả đúng bằng tên lớp viết thường -
                // đừng nối thêm lần nữa kẻo thành "...controllercontroller".
                $cls = basename($path, '.php');
                $sources[] = strtolower(
                    substr($cls, -10) === 'Controller' ? $cls : $cls.'controller'
                );
            }
            if (preg_match_all('/->l\(\s*(["\'])((?:(?!\1).)*)\1/s', $code, $m)) {
                foreach ($m[2] as $s) {
                    foreach ($sources as $src) {
                        $addHit($s, $src);
                    }
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
    // Xem ghi chú cùng chỗ trong generate_vn_admin_translations.php: ghi hỏng vì
    // quyền thì phải báo, không được im lặng rồi in ra con số như thể đã xong.
    if (file_put_contents($dir.'/translations/vn.php', $php) === false) {
        fwrite(STDERR, "LỖI: không ghi được {$dir}/translations/vn.php. Kiểm tra quyền:\n"
            ."  docker compose exec web chown -R www-data:www-data /var/www/html/modules\n");
        exit(1);
    }

    // index.php giữ nguyên nếp của dự án: chặn liệt kê thư mục
    if (!file_exists($dir.'/translations/index.php') && file_exists($moduleDir.'index.php')) {
        copy($moduleDir.'index.php', $dir.'/translations/index.php');
    }

    $totalKeys += count($lines);
    $report[] = sprintf('%-24s %3d chuỗi quét được, dịch %2d, ghi %3d khoá', $name, count($strings), $translated, count($lines));
}

echo implode("\n", $report)."\n\n";
echo 'Tổng số khoá đã ghi: '.$totalKeys."\n";
