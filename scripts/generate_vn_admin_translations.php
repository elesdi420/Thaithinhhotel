<?php
/**
 * Sinh tệp dịch tiếng Việt cho khu vực quản trị lõi (translations/vn/admin.php).
 *
 * Bối cảnh: khác với module (đã có translations/vn.php riêng), chuỗi $this->l()
 * gọi trực tiếp trong các AdminXxxController.php và trong helpers/list, helpers/kpi
 * tra vào translations/<iso>/admin.php - file này chưa từng tồn tại cho "vn", nên
 * toàn bộ tiêu đề cột, nút, ô lọc trong Danh mục/Đơn hàng/Bảng điều khiển vẫn tiếng
 * Anh dù menu bên trái (lấy từ bảng tab_lang) đã là tiếng Việt từ trước. Cùng một
 * gốc bệnh với mails/vn/ và themes/.../lang/vn.php: bật tiếng Việt nhưng thiếu tệp
 * dịch đi kèm.
 *
 * Cách hoạt động: Translate::getGenericAdminTranslation() tra theo thứ tự
 * 'AdminController'.md5(chuỗi), rồi 'Helper'.md5(chuỗi), rồi 'AdminTab'.md5(chuỗi)
 * mỗi khi không tìm thấy khoá riêng của class gọi ($class.md5(chuỗi)). Nghĩa là một
 * chuỗi ghi dưới khoá 'AdminController' áp dụng cho MỌI controller gọi đúng chuỗi
 * đó (AdminOrdersController, AdminProductsController,... đều định nghĩa lại
 * "Occupied Rooms" cho riêng mình nhưng dùng chung một bản dịch) - nên không cần
 * quét & dịch riêng từng file, một từ điển phẳng là đủ.
 *
 * Phạm vi: chỉ những chuỗi thực sự hiện ra trên các trang lễ tân/quản lý dùng hàng
 * ngày - Bảng điều khiển, Danh mục (loại phòng/phân loại), Quản lý Đơn hàng, và bộ
 * khung danh sách/toolbar dùng chung mọi nơi. Không đụng tới các trang quản trị hệ
 * thống nâng cao (modules, webservice,...) vì không ai trong quy trình vận hành
 * khách sạn nhìn tới - dịch thêm chỉ để "cho đủ" là việc thừa (YAGNI).
 *
 * Chuỗi không có trong từ điển bị bỏ qua - tiếp tục hiện tiếng Anh như hiện tại,
 * nên chạy script này không thể làm hỏng trang nào.
 *
 * Chạy: docker compose exec web php /var/www/html/scripts/generate_vn_admin_translations.php
 */

define('_PS_ADMIN_DIR_', dirname(__DIR__).'/admin2833tvua4');
require_once dirname(__DIR__).'/config/config.inc.php';

/** Chuỗi gốc => bản dịch. Tất cả ghi chung dưới khoá bucket 'AdminController'. */
$dict = array(
    // --- Khung danh sách (helpers/list) - lặp lại trên mọi trang danh sách ---
    'Bulk actions' => 'Thao tác hàng loạt',
    'Filters' => 'Bộ lọc',
    'Filters: ' => 'Bộ lọc: ',
    'Search' => 'Tìm kiếm',
    'Reset' => 'Đặt lại',
    'Yes' => 'Có',
    'No' => 'Không',
    'no' => 'không',
    'Enabled' => 'Đang bật',
    'Disabled' => 'Đang tắt',
    'Display' => 'Hiển thị',
    'Select all' => 'Chọn tất cả',
    'Unselect all' => 'Bỏ chọn tất cả',
    'Toggle list' => 'Thu gọn/mở rộng danh sách',
    'Refresh list' => 'Tải lại danh sách',
    'result(s)' => 'kết quả',
    'and stay' => 'và ở lại trang',
    'Shop' => 'Cơ sở',
    'Shop group' => 'Nhóm cơ sở',
    'Available Fields' => 'Cột có thể hiển thị',
    'Please fill at least one field to perform a search in this list.'
        => 'Vui lòng nhập ít nhất một ô để tìm kiếm trong danh sách này.',
    'No records found' => 'Không tìm thấy dữ liệu',
    'Show SQL query' => 'Hiện câu lệnh SQL',
    'Export to SQL Manager' => 'Xuất sang trình quản lý SQL',
    'Filter by %s' => 'Lọc theo %s',
    'You must select at least one item to perform a bulk action.'
        => 'Vui lòng chọn ít nhất một dòng để thực hiện thao tác hàng loạt.',

    // --- Toolbar chung (AdminController.php) ---
    'Add new' => 'Thêm mới',
    'Back to list' => 'Quay lại danh sách',
    'Cancel' => 'Huỷ',
    'Save' => 'Lưu',
    'Export' => 'Xuất dữ liệu',
    'Access denied' => 'Không có quyền truy cập',
    'Edit' => 'Sửa',
    'Delete' => 'Xoá',
    'Enable' => 'Bật',
    'Disable' => 'Tắt',
    'Enable selection' => 'Bật các mục đã chọn',
    'Disable selection' => 'Tắt các mục đã chọn',
    'Recommendations' => 'Gợi ý',

    // --- Khối KPI (helpers/kpi/row.tpl) ---
    'Refresh' => 'Tải lại',
    'Select KPIs' => 'Chọn chỉ số hiển thị',
    'Toggle View' => 'Đổi kiểu xem',

    // --- Chỉ số phòng trên Bảng điều khiển / Danh mục / Đơn hàng ---
    'Total Rooms' => 'Tổng số phòng',
    'Available Rooms' => 'Phòng còn trống',
    'Occupied Rooms' => 'Phòng đang ở',
    'Vacant Rooms' => 'Phòng trống',
    'Booked Rooms' => 'Phòng đã đặt',
    'Disabled Rooms' => 'Phòng đã khoá',
    'Disabled Room Types' => 'Loại phòng đã khoá',
    'Online Bookable Rooms' => 'Phòng đặt được online',
    'Offline Bookable Rooms' => 'Phòng đặt được offline',
    'Best Selling' => 'Bán chạy nhất',

    // --- Danh mục > Quản lý loại phòng (AdminProductsController) ---
    'Name' => 'Tên',
    'Reference' => 'Mã tham chiếu',
    'Category' => 'Danh mục',
    'Description' => 'Mô tả',
    'Displayed' => 'Hiển thị',
    'Position' => 'Vị trí',
    'Status' => 'Trạng thái',
    'Base Price' => 'Giá gốc',
    'Final price' => 'Giá cuối',
    'Hotel' => 'Cơ sở',
    'Location/Hotel' => 'Vị trí/Cơ sở',
    'Adults' => 'Người lớn',
    'Children' => 'Trẻ em',
    'Maximum Occupancy' => 'Sức chứa tối đa',
    'Additional Facilities' => 'Tiện nghi bổ sung',
    'Advance Payment' => 'Đặt cọc trước',
    'Show at front' => 'Hiện ở trang chủ',
    'Tax Rules' => 'Quy tắc thuế',
    'Default shop' => 'Cơ sở mặc định',
    'Features' => 'Đặc điểm',
    'Service Products' => 'Sản phẩm dịch vụ',
    'Services Products' => 'Sản phẩm dịch vụ',
    'Image' => 'Hình ảnh',

    // --- Quản lý Đơn hàng (AdminOrdersController) ---
    'Order date' => 'Ngày đặt',
    'Reservation Date' => 'Ngày đặt phòng',
    'Customer' => 'Khách hàng',
    'Email' => 'Email',
    'Phone' => 'Điện thoại',
    'Address' => 'Địa chỉ',
    'City' => 'Thành phố',
    'Country' => 'Quốc gia',
    'State' => 'Tỉnh/Bang',
    'Company' => 'Công ty',
    'Check In' => 'Nhận phòng',
    'Check Out' => 'Trả phòng',
    'Check-in' => 'Nhận phòng',
    'Check-out' => 'Trả phòng',
    'Date From' => 'Từ ngày',
    'Date To' => 'Đến ngày',
    'Stay Periods' => 'Khoảng thời gian ở',
    'Stay duration' => 'Số đêm ở',
    'Guests' => 'Số khách',
    'No. of rooms' => 'Số phòng',
    'Rooms' => 'Phòng',
    'Room Types' => 'Loại phòng',
    'Room type' => 'Loại phòng',
    'Room Status' => 'Trạng thái phòng',
    'Room status' => 'Trạng thái phòng',
    'Order Total' => 'Tổng đơn hàng',
    'Order Currency' => 'Đơn vị tiền tệ',
    'Order Source' => 'Nguồn đặt hàng',
    'Currency' => 'Đơn vị tiền tệ',
    'Amount Paid' => 'Đã thanh toán',
    'Due Amount' => 'Còn phải thu',
    'Payment' => 'Thanh toán',
    'PDF' => 'Xuất PDF',
    'Cancellation date' => 'Ngày huỷ',
    'Cancellation fee' => 'Phí huỷ',
    'Refunded / Cancelled Rooms' => 'Phòng đã hoàn tiền/Đã huỷ',

    // --- Tiêu đề trang & nút của Quản lý Đơn hàng ---
    'Orders' => 'Đơn hàng',
    'Create order' => 'Tạo đơn hàng',
    'Add new order' => 'Thêm đơn hàng mới',

    // --- Chỉ số doanh thu/vận hành trên trang Đơn hàng (renderKpis) ---
    'Total Sales' => 'Tổng doanh thu',
    'Total Due Amount' => 'Tổng còn phải thu',
    'Arrivals' => 'Khách đến',
    'Departures' => 'Khách đi',
    'Today' => 'Hôm nay',
    'Abandoned Carts' => 'Giỏ hàng bị bỏ dở',
    'Conversion Rate' => 'Tỷ lệ chuyển đổi',
    'Average Order Value' => 'Giá trị đơn trung bình',
    'Average Lead Time' => 'Thời gian đặt trước trung bình',
    'Average Guest Per Booking' => 'Số khách trung bình/đơn',
    'day' => 'ngày',
    'days' => 'ngày',
);

$targetFile = dirname(__DIR__).'/translations/vn/admin.php';
if (!is_dir(dirname($targetFile))) {
    mkdir(dirname($targetFile), 0755, true);
}

$lines = array();
$count = 0;
foreach ($dict as $source => $translated) {
    $key = 'AdminController'.md5($source);
    $lines[] = '$_LANGADM[\''.$key.'\'] = \''.addslashes($translated).'\';';
    $count++;
}

$php = "<?php\n\nglobal \$_LANGADM;\n\$_LANGADM = array();\n\n".implode("\n", $lines)."\n";
file_put_contents($targetFile, $php);

echo "Đã ghi {$count} khoá vào {$targetFile}\n";
