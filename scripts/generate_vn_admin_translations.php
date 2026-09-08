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

    // ================================================================
    // Đợt 2: soạn từ kết quả scripts/scan_admin_untranslated.php
    //
    // Quét thấy 4.637 chuỗi dịch được trong khu quản trị, còn thiếu 4.513.
    // Dịch hết là vô nghĩa: đuôi phân bố rất dài (1.000 chuỗi mới phủ 50% số
    // lần xuất hiện) và phần lớn nằm ở các trang lễ tân không bao giờ mở
    // (SQL manager, webservice, kho vận, nhà sản xuất, nhập/xuất CSV...).
    // Nên phạm vi đợt này là: chuỗi khung dùng chung tần suất cao + đúng các
    // trang nhân viên khách sạn thực sự thao tác hàng ngày.
    // ================================================================

    // --- Khung dùng chung, tần suất cao nhất toàn khu quản trị ---
    'ID' => 'Mã',
    'Save and stay' => 'Lưu và ở lại',
    'Delete selected' => 'Xoá mục đã chọn',
    'Delete selected items?' => 'Xoá các mục đã chọn?',
    'Are you sure?' => 'Bạn chắc chắn chứ?',
    'Invalid characters:' => 'Ký tự không hợp lệ:',
    'Forbidden characters:' => 'Ký tự bị cấm:',
    'Forbidden characters' => 'Ký tự bị cấm',
    'Shop association' => 'Gán cho cơ sở',
    'Quantity' => 'Số lượng',
    'From' => 'Từ',
    'To' => 'Đến',
    'Date' => 'Ngày',
    'Remove' => 'Bỏ',
    'Add' => 'Thêm',
    'Add New' => 'Thêm mới',
    'Close' => 'Đóng',
    'Update' => 'Cập nhật',
    'Duplicate' => 'Nhân bản',
    'Preview' => 'Xem trước',
    'View' => 'Xem',
    'Action' => 'Thao tác',
    'Active' => 'Đang bật',
    'Amount' => 'Số tiền',
    'Value' => 'Giá trị',
    'Group' => 'Nhóm',
    'Language' => 'Ngôn ngữ',
    'Title' => 'Tiêu đề',
    'Message' => 'Nội dung',
    'Options' => 'Tuỳ chọn',
    'Default' => 'Mặc định',
    'Not defined' => 'Chưa đặt',
    'Undefined value' => 'Giá trị chưa xác định',
    'Incorrect value' => 'Giá trị không đúng',
    'The status has been updated successfully.' => 'Đã cập nhật trạng thái.',
    'Failed to update the status' => 'Cập nhật trạng thái thất bại',
    'Update successful' => 'Cập nhật thành công',
    'You do not have the right permission' => 'Bạn không có quyền thực hiện',
    'Tax excluded' => 'Chưa gồm thuế',
    'Tax included' => 'Đã gồm thuế',
    'No tax' => 'Không thuế',
    'No Tax' => 'Không thuế',
    'Price (tax excl.)' => 'Giá (chưa gồm thuế)',
    'Unit Price (tax excl.)' => 'Đơn giá (chưa gồm thuế)',
    'Meta title' => 'Tiêu đề SEO',
    'Meta description' => 'Mô tả SEO',
    'Meta keywords' => 'Từ khoá SEO',
    'Friendly URL' => 'Đường dẫn thân thiện',
    'Adult' => 'Người lớn',
    'Child' => 'Trẻ em',
    'Duration' => 'Thời lượng',
    '/ night' => '/ đêm',
    'day(s)' => 'ngày',
    'minutes' => 'phút',
    'hours' => 'giờ',
    'megabytes' => 'megabyte',
    '30 days' => '30 ngày',

    // --- Danh mục > Quản lý loại phòng (AdminProducts) ---
    'Add new room type' => 'Thêm loại phòng mới',
    'Your room type will be saved as a draft.' => 'Loại phòng sẽ được lưu ở dạng nháp.',
    'Save and preview' => 'Lưu và xem trước',
    'Information' => 'Thông tin',
    'Prices' => 'Giá',
    'Images' => 'Hình ảnh',
    'Associations' => 'Liên kết',
    'Amenities' => 'Tiện nghi',
    'Occupancy' => 'Sức chứa',
    'Categories' => 'Danh mục',
    'Products' => 'Sản phẩm',
    'Hotels' => 'Cơ sở',
    'Customers' => 'Khách hàng',
    'Addresses' => 'Địa chỉ',
    'All' => 'Tất cả',
    'All customers' => 'Tất cả khách hàng',

    // --- Danh mục > Phân loại (AdminCategories) ---
    'Add new category' => 'Thêm phân loại mới',
    'Add new root category' => 'Thêm phân loại gốc',
    'Parent category' => 'Phân loại cha',
    'Category Cover Image' => 'Ảnh bìa phân loại',
    'Category thumbnail' => 'Ảnh thu nhỏ phân loại',
    'Top Category' => 'Phân loại hàng đầu',
    'Empty Categories' => 'Phân loại rỗng',
    'Disabled Categories' => 'Phân loại đã tắt',

    // --- Khuyến mại & Giảm giá > Quy tắc giá (AdminSpecificPriceRule) ---
    'Catalog price rules' => 'Quy tắc giá theo danh mục',
    'Add new catalog price rule' => 'Thêm quy tắc giá mới',
    'Reduction' => 'Mức giảm',
    'Reduction type' => 'Kiểu giảm',
    'Reduction with or without taxes' => 'Giảm trước hay sau thuế',
    'Percentage' => 'Phần trăm',
    'Leave base price' => 'Giữ nguyên giá gốc',
    'Beginning' => 'Bắt đầu',
    'End' => 'Kết thúc',
    'All currencies' => 'Mọi loại tiền',
    'All countries' => 'Mọi quốc gia',
    'All groups' => 'Mọi nhóm',

    // --- Quản trị hệ thống > Nhân viên (AdminEmployees) ---
    'Employees' => 'Nhân viên',
    'Add new employee' => 'Thêm nhân viên mới',
    'Employee options' => 'Tuỳ chọn nhân viên',
    'Profile' => 'Nhóm quyền',
    'First Name' => 'Tên',
    'Last Name' => 'Họ',
    'Email address' => 'Địa chỉ email',
    'Password' => 'Mật khẩu',
    'Password regeneration' => 'Đặt lại mật khẩu',
    'Default page' => 'Trang mặc định',
    'Theme' => 'Giao diện',
    'Back office theme.' => 'Giao diện khu quản trị.',
    'Admin menu orientation' => 'Hướng menu quản trị',

    // --- Tùy chọn cấu hình (AdminPreferences) ---
    'General' => 'Chung',
    'Notifications' => 'Thông báo',
    'Show notifications for new orders' => 'Báo khi có đơn mới',
    'Show notifications for new customers' => 'Báo khi có khách mới',
    'Show notifications for new messages' => 'Báo khi có tin nhắn mới',
    'Upload quota' => 'Hạn mức tải lên',
    'Maximum size for attachment' => 'Dung lượng tối đa của tệp đính kèm',
    "Maximum size for a product's image" => 'Dung lượng tối đa của ảnh sản phẩm',
    'Check the cookie\'s IP address' => 'Kiểm tra địa chỉ IP của cookie',
    'Lifetime of front office cookies' => 'Thời hạn cookie trang khách',
    'Lifetime of back office cookies' => 'Thời hạn cookie khu quản trị',
    'Automatically check for module updates' => 'Tự kiểm tra cập nhật module',

    // --- Chi tiết đơn hàng: màn hình lễ tân dùng nhiều nhất (AdminOrders) ---
    'Checked in' => 'Đã nhận phòng',
    'Checked out' => 'Đã trả phòng',
    'Alloted' => 'Đã xếp phòng',
    'Guest CheckIn - Checkout' => 'Nhận phòng - Trả phòng',
    'Change Order Status' => 'Đổi trạng thái đơn',
    'Add Room' => 'Thêm phòng',
    'Add Product' => 'Thêm dịch vụ',
    'Traveller' => 'Khách lưu trú',
    'Save Traveller' => 'Lưu thông tin khách',
    'Payment Detail' => 'Chi tiết thanh toán',
    'Order Payment' => 'Thanh toán đơn hàng',
    'Add Payment' => 'Thêm thanh toán',
    'Voucher' => 'Phiếu giảm giá',
    'Add Voucher' => 'Thêm phiếu giảm giá',
    'Booking Voucher' => 'Phiếu đặt phòng',
    'View booking voucher' => 'Xem phiếu đặt phòng',
    'Invoice' => 'Hoá đơn',
    'View invoice' => 'Xem hoá đơn',
    'View Hotel' => 'Xem cơ sở',
    'Documents' => 'Tài liệu',
    'Upload' => 'Tải lên',
    'Note' => 'Ghi chú',
    'Save Note' => 'Lưu ghi chú',
    'Remark' => 'Ghi chú',
    'Refund' => 'Hoàn tiền',
    'Initiate refund' => 'Tạo yêu cầu hoàn tiền',
    'Cancel bookings' => 'Huỷ đặt phòng',
    'Adult(s)' => 'Người lớn',
    'You do not have permission to edit this order.'
        => 'Bạn không có quyền sửa đơn hàng này.',
    'Selected room is not available for reallocation.'
        => 'Phòng đã chọn không thể chuyển sang được.',
    'Selected room is not available to swap.'
        => 'Phòng đã chọn không thể đổi chỗ được.',
    'Room in the booking is successfully reallocated'
        => 'Đã chuyển phòng cho đơn đặt thành công',
    'Room in the booking is successfully swapped'
        => 'Đã đổi phòng cho đơn đặt thành công',
    'Overbooking is successfully resolved' => 'Đã xử lý xong tình trạng trùng phòng',
    'Some error occured. Please try again.' => 'Có lỗi xảy ra. Vui lòng thử lại.',
    'No customer name found' => 'Không tìm thấy tên khách',
    'Manual order -- Employee:' => 'Đơn nhập tay -- Nhân viên:',

    // --- Loại phòng: thông báo và nhãn còn lại (AdminProducts) ---
    'Room Type' => 'Loại phòng',
    'Room Type Sales' => 'Doanh số theo loại phòng',
    'Statistics' => 'Thống kê',
    'Length of Stay' => 'Số đêm lưu trú',
    'Delete this room type' => 'Xoá loại phòng này',
    'Delete room type?' => 'Xoá loại phòng?',
    'Room type not found.' => 'Không tìm thấy loại phòng.',
    'Invalid room type ID %d.' => 'Mã loại phòng %d không hợp lệ.',
    'You must save room type before managing length of stay.'
        => 'Phải lưu loại phòng trước khi cấu hình số đêm lưu trú.',
    'You must save this room type before managing rooms.'
        => 'Phải lưu loại phòng trước khi quản lý các phòng.',
    'Add Hotel Before configurate this room type.'
        => 'Phải thêm cơ sở trước khi cấu hình loại phòng này.',
    'Room type can not be active as long as hotel is disabled.'
        => 'Không bật được loại phòng khi cơ sở đang tắt.',
    'Room types linked to inactive hotels cannot be enabled.'
        => 'Không bật được loại phòng thuộc cơ sở đang tắt.',
    'This room type must be in at least one category.'
        => 'Loại phòng phải thuộc ít nhất một phân loại.',
    'This room type must be in the default category.'
        => 'Loại phòng phải thuộc phân loại mặc định.',
    'An error occurred while updating the status.'
        => 'Có lỗi khi cập nhật trạng thái.',
    'The total number of rooms in all hotels.' => 'Tổng số phòng của tất cả cơ sở.',
    'The count of rooms that are currently occupied by guests.'
        => 'Số phòng đang có khách ở.',
    'The total number of rooms that are currently disabled.'
        => 'Số phòng đang bị khoá.',
    'The total number of room types that are currently disabled.'
        => 'Số loại phòng đang bị khoá.',
    '%d Day' => '%d ngày',
    '%d Days' => '%d ngày',

    // --- Bảng điều khiển: bộ lọc ngày và chọn cơ sở (AdminDashboard) ---
    'Calendar' => 'Lịch',
    'Day' => 'Ngày',
    'Month' => 'Tháng',
    'Year' => 'Năm',
    'From:' => 'Từ:',
    'To:' => 'Đến:',
    '(from %s to %s)' => '(từ %s đến %s)',
    'All Hotels' => 'Tất cả cơ sở',
    'No permission for requested hotel.' => 'Bạn không có quyền xem cơ sở này.',
    'Net Profit settings' => 'Cấu hình lợi nhuận ròng',
    'Other fees per order' => 'Chi phí khác trên mỗi đơn',
    'Average bank fees per payment method' => 'Phí ngân hàng trung bình theo phương thức',
    'Average gross operating cost percentage' => 'Tỷ lệ chi phí vận hành gộp trung bình',

    // --- Nhãn còn sót trên dải KPI trang Đơn hàng (phát hiện khi soi ảnh chụp) ---
    'tax excl.' => 'chưa gồm thuế',
    'tax incl.' => 'đã gồm thuế',
    'From %s to %s' => 'Từ %s đến %s',
    'Adults' => 'Người lớn',
    'Children' => 'Trẻ em',

    // --- Thanh tiêu đề dùng chung cho MỌI trang quản trị (header.tpl) ---
    // Ba menu thả xuống cạnh chuông thông báo và menu Quick Access. Không nhìn
    // thấy trên ảnh chụp vì phải bấm mới mở, nhưng badge đang hiện số nên chắc
    // chắn nhân viên sẽ bấm vào.
    'Latest Orders' => 'Đơn mới nhất',
    'Latest Registrations' => 'Khách đăng ký mới nhất',
    'Latest Messages' => 'Tin nhắn mới nhất',
    'No new orders have been placed on your shop.' => 'Chưa có đơn đặt phòng mới.',
    'No new customers have registered on your shop.' => 'Chưa có khách đăng ký mới.',
    'No new messages have been posted on your shop.' => 'Chưa có tin nhắn mới.',
    'A new order has been placed on your shop.' => 'Vừa có đơn đặt phòng mới.',
    'A new customer registered on your shop.' => 'Vừa có khách đăng ký mới.',
    'A new message was posted on your shop.' => 'Vừa có tin nhắn mới.',
    'Show all orders' => 'Xem tất cả đơn',
    'Show all customers' => 'Xem tất cả khách',
    'Show all messages' => 'Xem tất cả tin nhắn',
    'View this order' => 'Xem đơn này',
    'Read this message' => 'Đọc tin nhắn này',
    'Order number:' => 'Mã đơn:',
    'Customer name:' => 'Tên khách:',
    'Total:' => 'Tổng cộng:',
    'From:' => 'Từ:',

    'Quick Access' => 'Truy cập nhanh',
    'Add current page to QuickAccess' => 'Thêm trang này vào Truy cập nhanh',
    'Remove from QuickAccess' => 'Bỏ khỏi Truy cập nhanh',
    'Please name this shortcut:' => 'Đặt tên cho lối tắt này:',
    'New room type' => 'Loại phòng mới',
    'New voucher' => 'Phiếu giảm giá mới',
    'Search for a product' => 'Tìm loại phòng',
    'Choose language' => 'Chọn ngôn ngữ',
    'My preferences' => 'Tuỳ chọn của tôi',
    'My site' => 'Trang của tôi',
    'View my shop' => 'Xem trang khách',
    'Sign out' => 'Đăng xuất',
    'Me' => 'Tôi',
    'Site' => 'Trang',

    'Maintenance mode' => 'Chế độ bảo trì',
    'Your site is in maintenance.' => 'Trang đang ở chế độ bảo trì.',
    'Debug mode' => 'Chế độ gỡ lỗi',
    'Your site is in debug mode.' => 'Trang đang ở chế độ gỡ lỗi.',
    'For security reasons, you must also delete the /install folder.'
        => 'Vì lý do bảo mật, cần xoá thêm thư mục /install.',

    // --- Ô tìm kiếm ở góc trái, có trên mọi trang (search_form.tpl) ---
    'What are you looking for?' => 'Bạn đang tìm gì?',
    'Everywhere' => 'Mọi nơi',
    'Catalog' => 'Danh mục',
    'Carts' => 'Giỏ hàng',
    'Invoices' => 'Hoá đơn',
    'Modules' => 'Module',
    'Cart ID' => 'Mã giỏ hàng',
    'Order ID' => 'Mã đơn hàng',
    'Invoice Number' => 'Số hoá đơn',
    'Module name' => 'Tên module',
    'Email, name...' => 'Email, tên...',
    'Room Types, Service products...' => 'Loại phòng, dịch vụ...',
    'by name' => 'theo tên',
    'by ip address' => 'theo địa chỉ IP',

    // --- Form sửa loại phòng, tab Thông tin (AdminProducts) ---
    'Edit: %s' => 'Sửa: %s',
    'Recommended Modules and Services' => 'Module và dịch vụ gợi ý',
    'Loading...' => 'Đang tải...',
    'Hotel once assigned cannot be reassigned'
        => 'Đã gán cơ sở thì không đổi sang cơ sở khác được',
    'Reference code' => 'Mã tham chiếu',
    'EAN-13 or JAN barcode' => 'Mã vạch EAN-13 hoặc JAN',
    'UPC barcode' => 'Mã vạch UPC',
    'Short description' => 'Mô tả ngắn',
    'Visibility' => 'Phạm vi hiển thị',
    'Catalog only' => 'Chỉ trong danh mục',
    'Search only' => 'Chỉ khi tìm kiếm',
    'Nowhere' => 'Không hiện ở đâu',
    'Show at front office' => 'Hiện ở trang khách',
    'Available for order' => 'Cho phép đặt',
    'Show price' => 'Hiện giá',
    'Online only (not sold in your retail store)' => 'Chỉ bán online',
    'Related room type' => 'Loại phòng liên quan',
    'No related room type.' => 'Không có loại phòng liên quan.',
    'Redirect when disabled' => 'Chuyển hướng khi bị tắt',
    'No redirect (404)' => 'Không chuyển hướng (404)',
    'Redirected permanently (301)' => 'Chuyển hướng vĩnh viễn (301)',
    'Redirected temporarily (302)' => 'Chuyển hướng tạm thời (302)',
    '404 Not Found = Do not redirect and display a 404 page.'
        => '404 Not Found = Không chuyển hướng, hiện trang báo lỗi 404.',
    '301 Moved Permanently = Permanently display another room type instead.'
        => '301 Moved Permanently = Hiện vĩnh viễn một loại phòng khác thay thế.',
    '302 Moved Temporarily = Temporarily display another room type instead.'
        => '302 Moved Temporarily = Hiện tạm thời một loại phòng khác thay thế.',

    // Hộp thoại nhân bản loại phòng và ghi chú về thứ tự hiển thị.
    'Duplication options' => 'Tuỳ chọn nhân bản',
    'Duplicate images' => 'Nhân bản cả hình ảnh',
    'Select hotel' => 'Chọn cơ sở',
    'Submit' => 'Gửi',
    'Please note that position numbering starts from 0. A position of 0 means room type will be displayed at the topmost position.'
        => 'Lưu ý: thứ tự đánh số từ 0. Vị trí 0 nghĩa là loại phòng hiện trên cùng.',
    'You can manage positions of the room types of this hotel from here.'
        => 'Bạn có thể sắp thứ tự các loại phòng của cơ sở này tại đây.',
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
// Kiểm tra ghi có thành công không. Chạy script bằng www-data trong khi tệp cũ
// do root tạo thì file_put_contents thất bại lặng lẽ, script vẫn in "đã ghi N
// khoá" còn tệp thì y nguyên - đã mất công truy vết vì đúng cái bẫy này.
if (file_put_contents($targetFile, $php) === false) {
    fwrite(STDERR, "LỖI: không ghi được {$targetFile}. Kiểm tra quyền:\n"
        ."  docker compose exec web chown -R www-data:www-data /var/www/html/translations\n");
    exit(1);
}

echo "Đã ghi {$count} khoá vào {$targetFile}\n";
