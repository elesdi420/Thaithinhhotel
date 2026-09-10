<?php
/**
 * Sinh tệp dịch tiếng Việt cho các thông báo lỗi (translations/vn/errors.php).
 *
 * Đây là từ điển THỨ BA, tách hẳn khỏi hai cái đã có:
 *   - translations/vn/admin.php        <- $this->l() trong AdminXxxController
 *   - modules/<mod>/translations/vn.php <- $this->l() trong module
 *   - translations/vn/errors.php       <- Tools::displayError()   (tệp này)
 *
 * Tools::displayError() không dùng cơ chế Translate:: nào cả. Nó nạp thẳng
 * translations/<iso>/errors.php, đọc biến toàn cục $_ERRORS, và tra bằng
 *     md5(str_replace('\'', '\\\'', $chuoi))
 * tức md5 của chuỗi Ở DẠNG ĐÃ ESCAPE DẤU NHÁY như khi viết trong mã nguồn -
 * không phải md5 của chuỗi lúc chạy. Sai chỗ này thì mọi khoá đều trượt.
 * Không có tệp -> $_ERRORS không phải mảng -> hàm trả về nguyên tiếng Anh,
 * âm thầm, không cảnh báo. Đó là lý do 7 câu chặn của luồng nhận/trả phòng
 * vẫn tiếng Anh dù đã nằm trong admin.php.
 *
 * Phạm vi: cả dự án có 1256 chuỗi displayError khác nhau. Tệp này chỉ dịch
 * những câu khách hoặc lễ tân thực sự gặp khi đặt phòng, thanh toán, đăng
 * nhập và đổi trạng thái phòng. Phần còn lại (lỗi nội bộ, thông báo cấu hình
 * module ít dùng) để nguyên tiếng Anh - dịch hết là việc của gói ngôn ngữ,
 * không phải của bản demo này.
 *
 * Chạy:
 *   docker compose exec -u www-data web php /var/www/html/scripts/generate_vn_errors.php
 */

$dict = array(

    // --- Luồng nhận / trả phòng (lễ tân bấm hằng ngày) ---
    'Invalid booking status found.' => 'Trạng thái đặt phòng không hợp lệ.',
    'Invalid dates found.' => 'Ngày không hợp lệ.',
    'Date should be between booking from date and to date.' => 'Ngày phải nằm trong khoảng từ ngày đến ngày của lượt đặt phòng.',
    'You cannot update this room\'s booking status as a new booking has been created for this room. Please change the room to update the status.' => 'Không đổi được trạng thái phòng này vì đã có lượt đặt mới cho phòng. Hãy đổi phòng rồi cập nhật lại trạng thái.',
    'Room status must be set to Check-In before setting the room status to Check-Out.' => 'Phải nhận phòng trước rồi mới trả phòng được.',
    'Check-Out date can not be before Check-In date (%s)' => 'Ngày trả phòng không được trước ngày nhận phòng (%s)',
    'You cannot checkout the last room while there are pending bills for this order.' => 'Không trả được phòng cuối cùng khi đơn này vẫn còn hoá đơn chưa thanh toán.',

    // --- Khách chọn phòng và số người ---
    'Invalid number of adults for Room %s.' => 'Số người lớn không hợp lệ cho phòng %s.',
    'Invalid number of children for Room %s.' => 'Số trẻ em không hợp lệ cho phòng %s.',
    'Please provide all children age for Room %s.' => 'Vui lòng nhập tuổi của tất cả trẻ em cho phòng %s.',
    'Invalid children age for Room %s.' => 'Tuổi trẻ em không hợp lệ cho phòng %s.',
    'Room %s cannot have adults more than %s adults' => 'Phòng %s không chứa quá %s người lớn.',
    'Room %s cannot have children more than %s children' => 'Phòng %s không chứa quá %s trẻ em.',
    'Room %s cannot have total guests more than %s' => 'Phòng %s không chứa quá %s khách.',
    'Invalid occupnacy.' => 'Số khách không hợp lệ.',
    'Room not found. Please try reloading the page.' => 'Không tìm thấy phòng. Vui lòng tải lại trang.',
    'Product not found' => 'Không tìm thấy sản phẩm.',
    'This product is no longer available.' => 'Sản phẩm này không còn nữa.',
    'This product does not exist in our store.' => 'Sản phẩm này không có trên hệ thống.',
    'You can only order one quantity for this product.' => 'Sản phẩm này chỉ đặt được một suất.',

    // --- Khách chọn ngày ---
    'You can\'t book room before current date' => 'Không đặt phòng cho ngày đã qua.',
    'Check-out date must be after check-in date' => 'Ngày trả phòng phải sau ngày nhận phòng.',
    'You can\'t book room after date ' => 'Không đặt phòng sau ngày ',
    'All rooms are sold out for the selected dates. Please try with different dates.' => 'Đã hết phòng trong khoảng ngày bạn chọn. Vui lòng chọn ngày khác.',
    'Rooms are unavailable. Please try with different dates' => 'Không còn phòng trống. Vui lòng chọn ngày khác.',
    'Check In time is invalid.' => 'Giờ nhận phòng không hợp lệ.',
    'Check out time is invalid.' => 'Giờ trả phòng không hợp lệ.',
    'Check Out time must be before Check In time.' => 'Giờ trả phòng phải trước giờ nhận phòng.',

    // --- Giỏ hàng và dịch vụ đi kèm ---
    'Impossible to add the product to the cart. Please refresh page.' => 'Không thêm được vào giỏ. Vui lòng tải lại trang.',
    'Can not add product without room in cart' => 'Cần chọn phòng trước khi thêm dịch vụ.',
    'Cannot add product without a hotel.' => 'Cần chọn cơ sở trước.',
    'Cannot add product without a option.' => 'Cần chọn một tuỳ chọn cho sản phẩm.',
    'Cannot add service without a option.' => 'Cần chọn một tuỳ chọn cho dịch vụ.',
    'This Service is not available.' => 'Dịch vụ này hiện không có.',
    'This Service is not available with selected room.' => 'Dịch vụ này không áp dụng cho phòng đã chọn.',
    'Service not Found' => 'Không tìm thấy dịch vụ.',
    'Unable to update services. Please try reloading the page.' => 'Không cập nhật được dịch vụ. Vui lòng tải lại trang.',
    'The selected option is not available.' => 'Tuỳ chọn đã chọn hiện không có.',
    'You already have the maximum quantity available for this product.' => 'Bạn đã đặt tối đa số lượng cho sản phẩm này.',
    'The quantity you\'ve entered is invalid.' => 'Số lượng bạn nhập không hợp lệ.',
    'Null quantity.' => 'Số lượng bằng không.',
    'You must add %d minimum quantity' => 'Phải đặt tối thiểu %d.',
    'There isn\'t enough product in stock.' => 'Không đủ hàng.',
    'An error occurred while cancelling the booking.' => 'Có lỗi khi huỷ đặt phòng.',
    'An error occurred while cancelling the product.' => 'Có lỗi khi huỷ sản phẩm.',
    'An error occurred while processing your request.' => 'Có lỗi khi xử lý yêu cầu của bạn.',

    // --- Đăng nhập, đăng ký, tài khoản ---
    'Invalid email address.' => 'Địa chỉ email không hợp lệ.',
    'An email address required.' => 'Vui lòng nhập email.',
    'Invalid password.' => 'Mật khẩu không hợp lệ.',
    'Password is required.' => 'Vui lòng nhập mật khẩu.',
    'Authentication failed.' => 'Đăng nhập không thành công.',
    'The customer could not be found.' => 'Không tìm thấy tài khoản.',
    'An account using this email address has already been registered.' => 'Email này đã được đăng ký.',
    'An account using this email address has already been registered. Please choose another one or sign in.' => 'Email này đã được đăng ký. Vui lòng dùng email khác hoặc đăng nhập.',
    'You are already registered as a guest with this email address.' => 'Email này đã dùng để đặt phòng không cần tài khoản.',
    'You cannot create a guest account.' => 'Không tạo được tài khoản khách.',
    'Your guest account could not be transformed to a customer account.' => 'Không chuyển được tài khoản khách thành tài khoản thành viên.',
    'Your account isn\'t available at this time, please contact us' => 'Tài khoản của bạn hiện chưa dùng được, vui lòng liên hệ khách sạn.',
    'An error occurred while creating your account.' => 'Có lỗi khi tạo tài khoản.',
    'Invalid date of birth.' => 'Ngày sinh không hợp lệ.',
    'Invalid date of birth' => 'Ngày sinh không hợp lệ',
    'You can regenerate your password only every %d minute(s)' => 'Mỗi %d phút mới đặt lại mật khẩu được một lần.',
    'The identification number is incorrect or has already been used.' => 'Mã xác nhận sai hoặc đã dùng rồi.',
    'Invalid token.' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.',
    'Invalid key.' => 'Mã không hợp lệ.',

    // --- Địa chỉ và thông tin liên hệ ---
    'You must register at least one phone number.' => 'Vui lòng nhập ít nhất một số điện thoại.',
    'Phone number is required.' => 'Vui lòng nhập số điện thoại.',
    'Phone number is required field.' => 'Vui lòng nhập số điện thoại.',
    'Please enter a valid phone number.' => 'Số điện thoại không hợp lệ.',
    'Invalid Phone number.' => 'Số điện thoại không hợp lệ.',
    'Phone is required.' => 'Vui lòng nhập số điện thoại.',
    'Address is required field.' => 'Vui lòng nhập địa chỉ.',
    'Address is invalid.' => 'Địa chỉ không hợp lệ.',
    'City is required field.' => 'Vui lòng nhập tỉnh / thành phố.',
    'Enter a Valid City Name.' => 'Tên tỉnh / thành phố không hợp lệ.',
    'Country is required field.' => 'Vui lòng chọn quốc gia.',
    'Country is invalid' => 'Quốc gia không hợp lệ',
    'country is invalid.' => 'Quốc gia không hợp lệ.',
    'This country is not active.' => 'Quốc gia này chưa được mở.',
    'This country requires you to choose a State.' => 'Quốc gia này yêu cầu chọn tỉnh / bang.',
    'This country requires you to chose a State.' => 'Quốc gia này yêu cầu chọn tỉnh / bang.',
    'State is invalid.' => 'Tỉnh / bang không hợp lệ.',
    'State is required field.' => 'Vui lòng chọn tỉnh / bang.',
    'The specified state is not located in this country.' => 'Tỉnh / bang này không thuộc quốc gia đã chọn.',
    'A Zip / Postal code is required.' => 'Vui lòng nhập mã bưu chính.',
    'A Zip/Postal code is required.' => 'Vui lòng nhập mã bưu chính.',
    'The Zip / Postal code is invalid.' => 'Mã bưu chính không hợp lệ.',
    'The Zip/Postal code is invalid.' => 'Mã bưu chính không hợp lệ.',
    'The Zip/Postal code you have entered is invalid. It must follow this format: %s' => 'Mã bưu chính không hợp lệ. Định dạng đúng: %s',
    'The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s' => 'Mã bưu chính không hợp lệ. Định dạng đúng: %s',
    'The alias "%s" has already been used. Please select another one.' => 'Tên gợi nhớ "%s" đã dùng rồi. Vui lòng đặt tên khác.',
    'An error occurred while updating your address.' => 'Có lỗi khi cập nhật địa chỉ.',
    'An error occurred while creating your address.' => 'Có lỗi khi tạo địa chỉ.',
    'It is not possible to deliver this product to the selected address.' => 'Không giao được tới địa chỉ đã chọn.',
    'The Fax is invalid.' => 'Số fax không hợp lệ.',

    // --- Liên hệ, đánh giá, tệp đính kèm ---
    'Name is required.' => 'Vui lòng nhập họ tên.',
    'Invalid name.' => 'Họ tên không hợp lệ.',
    'The title cannot be blank.' => 'Vui lòng nhập tiêu đề.',
    'The message cannot be blank.' => 'Vui lòng nhập nội dung.',
    'Invalid message' => 'Nội dung không hợp lệ',
    'Please choose who to send the message to.' => 'Vui lòng chọn nơi nhận.',
    'Your message has already been sent.' => 'Tin nhắn đã được gửi trước đó.',
    'An error occurred while sending the message.' => 'Có lỗi khi gửi tin nhắn.',
    'The email cannot be sent.' => 'Không gửi được email.',
    'Rating must be between 1 and 5.' => 'Điểm đánh giá phải từ 1 đến 5.',
    'Bad file extension' => 'Định dạng tệp không được chấp nhận.',
    'An error occurred during the file-upload process.' => 'Có lỗi khi tải tệp lên.',
    'An error occurred during the image upload process.' => 'Có lỗi khi tải ảnh lên.',
    'Please fill in all of the required fields, and then save your customizations.' => 'Vui lòng điền đủ các ô bắt buộc rồi lưu lại.',
);

$targetFile = dirname(__DIR__).'/translations/vn/errors.php';
if (!is_dir(dirname($targetFile))) {
    mkdir(dirname($targetFile), 0755, true);
}

$lines = array();
foreach ($dict as $source => $translated) {
    // Đúng công thức của Tools::displayError - md5 của chuỗi ở dạng escape.
    $key = md5(str_replace('\'', '\\\'', $source));
    $lines[] = '$_ERRORS[\''.$key.'\'] = \''.addslashes($translated).'\';';
}

$php = "<?php\n\nglobal \$_ERRORS;\n\$_ERRORS = array();\n\n".implode("\n", $lines)."\n";
// Cùng cái bẫy như hai bộ sinh kia: chạy bằng www-data đè lên tệp do root tạo
// thì file_put_contents trả về false lặng lẽ, script vẫn báo thành công.
if (file_put_contents($targetFile, $php) === false) {
    fwrite(STDERR, "LỖI: không ghi được {$targetFile}. Kiểm tra quyền:\n"
        ."  docker compose exec web chown -R www-data:www-data /var/www/html/translations\n");
    exit(1);
}

echo 'Đã ghi '.count($dict)." khoá vào {$targetFile}\n";
