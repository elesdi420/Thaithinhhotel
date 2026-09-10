<?php
/**
 * Xuất các sửa đổi NỘI DUNG & CẤU HÌNH ra docker/db/zz-content-fixes.sql.
 *
 * VÌ SAO CẦN
 * docker/db/init.sql vừa là lược đồ, vừa là dữ liệu mẫu, vừa là nội dung sản
 * phẩm. Ba thứ có vòng đời khác nhau nhưng nằm chung một tệp, nên bất kỳ ai
 * mariadb-dump từ máy mình cũng ghi đè cả ba. Đã mất hai lần theo đúng cách đó:
 * tên khách sạn về "CMS Hotel", trang chủ bày lại 4 phòng demo giá 1.375đ, địa
 * chỉ trên hoá đơn về Montgomery Alabama, tiếng Anh bật lại kèm ba trang pháp
 * lý còn nội dung demo. Không lần nào báo lỗi - trang vẫn trả 200.
 *
 * CÁCH CHỮA
 * MariaDB chạy MỌI tệp .sql trong /docker-entrypoint-initdb.d theo thứ tự bảng
 * chữ cái. Tệp sinh ra ở đây tên bắt đầu bằng "zz-" nên luôn chạy SAU init.sql
 * ("z" > "i"). Nhờ vậy hệ thống tự chữa: init.sql có bị ghi đè bằng bản cũ thì
 * nội dung vẫn được áp lại, và bản dump lần sau đã sẵn có các sửa đổi này.
 *
 * PHẠM VI
 * Chỉ nội dung và cấu hình - thứ gõ tay, không script nào sinh lại được.
 * KHÔNG gồm 36 đơn đặt phòng mẫu: chúng đã có scripts/seed_demo_bookings.php,
 * nhồi vào đây là nhân đôi nguồn sự thật. Mất đơn mẫu thì chạy lại một lệnh.
 *
 * Mọi câu lệnh sinh ra đều idempotent (UPDATE/REPLACE), chạy bao nhiêu lần cũng
 * ra một kết quả.
 *
 * Chạy: docker compose exec -u www-data web php /var/www/html/scripts/export_content_fixes.php
 */

$root = dirname(__DIR__);
define('_PS_ADMIN_DIR_', $root.'/admin2833tvua4');
require_once $root.'/config/config.inc.php';

$db = Db::getInstance();
$out = array();
$count = 0;

/** Bọc giá trị thành chuỗi SQL an toàn. */
$q = function ($v) {
    return $v === null ? 'NULL' : "'".pSQL($v, true)."'";
};

$out[] = "-- Sinh tự động bởi scripts/export_content_fixes.php - ĐỪNG SỬA TAY.";
$out[] = "-- Chạy sau init.sql nhờ tiền tố 'zz-'. Xem chú thích đầu script đó.";
$out[] = "-- Sinh lúc: ".date('Y-m-d H:i:s');
$out[] = "";
$out[] = "SET NAMES utf8mb4;";
$out[] = "";

// ---- 1. Cấu hình thương hiệu và ngôn ngữ mặc định ----
$out[] = "-- Thương hiệu: đã hai lần bị dump cũ đưa về 'CMS Hotel'.";
$cfgNames = array(
    'PS_SHOP_NAME', 'WK_TITLE_HEADER_BLOCK', 'WK_HTL_CHAIN_NAME',
    'PS_SHOP_ADDR1', 'PS_LANG_DEFAULT',
);
foreach ($cfgNames as $name) {
    $v = Configuration::getGlobalValue($name);
    if ($v === false || $v === null) {
        continue;
    }
    $out[] = "UPDATE `PREFIX_configuration` SET `value` = ".$q($v)." WHERE `name` = ".$q($name).";";
    $count++;
}
// ---- 1b. Cấu hình VietQR ----
// VIETQR_WEBHOOK_SECRET phải rỗng trong seed. Bản dump cũ từng mang lại giá trị
// "secret123"; kho mã này công khai nên một secret nằm trong seed thì không còn
// là secret, và tệ hơn: nó khiến cổng webhook mở với một khoá ai cũng đoán được.
// Để rỗng thì hàm kiểm chữ ký thất bại đóng - đúng hành vi mong muốn khi chưa
// cấu hình .env. Tên chủ tài khoản thì ngược lại, phải có, kẻo mã QR in ra sai tên.
foreach (array('VIETQR_ACCOUNT_NAME', 'VIETQR_ACCOUNT_NO', 'VIETQR_BANK_BIN', 'VIETQR_WEBHOOK_SECRET') as $name) {
    $v = Configuration::getGlobalValue($name);
    if ($v === false || $v === null) {
        continue;
    }
    if ($name === 'VIETQR_WEBHOOK_SECRET') {
        $v = '';
    }
    $out[] = "UPDATE `PREFIX_configuration` SET `value` = ".$q($v)." WHERE `name` = ".$q($name).";";
    $count++;
}

// ---- 1c. Ba khoi noi dung trang chu (luu theo ngon ngu) ----
// configuration_lang chu khong phai configuration, nen nhom o tren khong cham toi.
// Ban goc QloApps de nguyen van demo tieng Anh o CA hai ngon ngu - tuc la trang chu
// tieng Viet van hien "Amenities / Our Rooms / Explore the Interiors!" kem doan mo ta
// quang cao spa, be boi ngoai troi va nha hang cao cap ma khach san chua he xac nhan
// co (xem AI-5). Ban dich chi neu nhung tien nghi da co trong qlo_htl_amenity.
// Chi xuat ban tieng Viet: ghi de ca id_lang=1 thi ban tieng Anh se thanh tieng Viet.
$cfgLangNames = array(
    'HOTEL_AMENITIES_HEADING', 'HOTEL_AMENITIES_DESCRIPTION',
    'HOTEL_INTERIOR_HEADING', 'HOTEL_INTERIOR_DESCRIPTION',
    'HOTEL_ROOM_DISPLAY_HEADING', 'HOTEL_ROOM_DISPLAY_DESCRIPTION',
);
foreach ($db->executeS(
    'SELECT c.`name`, cl.`id_lang`, cl.`value`
       FROM `'._DB_PREFIX_.'configuration_lang` cl
       JOIN `'._DB_PREFIX_.'configuration` c ON c.`id_configuration` = cl.`id_configuration`
      WHERE cl.`id_lang` = 2 AND c.`name` IN ("'.implode('","', $cfgLangNames).'")'
) as $r) {
    $out[] = "UPDATE `PREFIX_configuration_lang` cl"
        ." JOIN `PREFIX_configuration` c ON c.`id_configuration` = cl.`id_configuration`"
        ." SET cl.`value` = ".$q($r['value'])
        ." WHERE c.`name` = ".$q($r['name'])." AND cl.`id_lang` = ".(int) $r['id_lang'].";";
    $count++;
}

// Email ho tro hien o thanh dau moi trang. Ban seed mang gia tri hotelprime@htl.com -
// ten thuong hieu cua ban demo QloApps ("Hotel Prime"), khong phai cua khach san nay.
$supportEmail = Configuration::getGlobalValue('WK_CUSTOMER_SUPPORT_EMAIL');
if ($supportEmail !== false && $supportEmail !== null) {
    $out[] = "UPDATE `PREFIX_configuration` SET `value` = ".$q($supportEmail)
        ." WHERE `name` = 'WK_CUSTOMER_SUPPORT_EMAIL';";
    $count++;
}

$shopName = $db->getValue('SELECT name FROM `'._DB_PREFIX_.'shop` WHERE id_shop = 1');
$out[] = "UPDATE `PREFIX_shop` SET `name` = ".$q($shopName)." WHERE `id_shop` = 1;";
$count++;
$out[] = "";

// ---- 2. Ngôn ngữ đang bật ----
$out[] = "-- Tiếng Anh tắt cho tới khi khách duyệt bản dịch ba trang pháp lý.";
foreach ($db->executeS('SELECT id_lang, active FROM `'._DB_PREFIX_.'lang`') as $r) {
    $out[] = "UPDATE `PREFIX_lang` SET `active` = ".(int) $r['active']." WHERE `id_lang` = ".(int) $r['id_lang'].";";
    $count++;
}
$out[] = "";

// ---- 3. Meta SEO ----
$out[] = "-- Meta SEO các trang chính.";
$metas = $db->executeS(
    'SELECT id_meta, id_lang, title, description, keywords FROM `'._DB_PREFIX_.'meta_lang`
     WHERE title LIKE "%Thaithinh%" OR description LIKE "%Thaithinh%"'
);
foreach ($metas as $r) {
    $out[] = "UPDATE `PREFIX_meta_lang` SET `title` = ".$q($r['title']).", `description` = ".$q($r['description'])
        .", `keywords` = ".$q($r['keywords'])." WHERE `id_meta` = ".(int) $r['id_meta']." AND `id_lang` = ".(int) $r['id_lang'].";";
    $count++;
}
$out[] = "";

// ---- 4. Chính sách giờ nhận / trả phòng ----
$out[] = "-- Giờ nhận 14:00, trả 12:00 - từng bị đưa về bản tiếng Anh.";
foreach ($db->executeS('SELECT id, id_lang, policies FROM `'._DB_PREFIX_.'htl_branch_info_lang` WHERE id_lang = 2') as $r) {
    $out[] = "UPDATE `PREFIX_htl_branch_info_lang` SET `policies` = ".$q($r['policies'])
        ." WHERE `id` = ".(int) $r['id']." AND `id_lang` = 2;";
    $count++;
}
$out[] = "";

// ---- 5. Tên menu khu quản trị ----
$out[] = "-- Tên menu quản trị (bảng tab_lang), khoảng 100 mục.";
foreach ($db->executeS('SELECT id_tab, name FROM `'._DB_PREFIX_.'tab_lang` WHERE id_lang = 2 AND name <> ""') as $r) {
    $out[] = "UPDATE `PREFIX_tab_lang` SET `name` = ".$q($r['name'])." WHERE `id_tab` = ".(int) $r['id_tab']." AND `id_lang` = 2;";
    $count++;
}
$out[] = "";

// ---- 6. Điều hướng trang khách ----
$out[] = "-- Điều hướng trang khách + ẩn mục trỏ tới khối lời chứng thực đã tắt.";
foreach ($db->executeS('SELECT id_navigation_link, name FROM `'._DB_PREFIX_.'htl_custom_navigation_link_lang` WHERE id_lang = 2 AND name <> ""') as $r) {
    $out[] = "UPDATE `PREFIX_htl_custom_navigation_link_lang` SET `name` = ".$q($r['name'])
        ." WHERE `id_navigation_link` = ".(int) $r['id_navigation_link']." AND `id_lang` = 2;";
    $count++;
}
foreach ($db->executeS('SELECT id_navigation_link, active FROM `'._DB_PREFIX_.'htl_custom_navigation_link`') as $r) {
    $out[] = "UPDATE `PREFIX_htl_custom_navigation_link` SET `active` = ".(int) $r['active']
        ." WHERE `id_navigation_link` = ".(int) $r['id_navigation_link'].";";
    $count++;
}
$out[] = "";

// ---- 7. Truy cập nhanh ----
$out[] = "-- Mục Truy cập nhanh trên thanh tiêu đề.";
foreach ($db->executeS('SELECT id_quick_access, name FROM `'._DB_PREFIX_.'quick_access_lang` WHERE id_lang = 2') as $r) {
    $out[] = "UPDATE `PREFIX_quick_access_lang` SET `name` = ".$q($r['name'])
        ." WHERE `id_quick_access` = ".(int) $r['id_quick_access']." AND `id_lang` = 2;";
    $count++;
}
$out[] = "";

// ---- 8. Trang CMS ----
$out[] = "-- Năm trang CMS: Giới thiệu và Thanh toán an toàn được viết lại hoàn toàn,";
$out[] = "-- ba trang pháp lý tiếng Việt đã soạn từ trước.";
foreach ($db->executeS('SELECT id_cms, id_lang, meta_title, meta_description, content FROM `'._DB_PREFIX_.'cms_lang`') as $r) {
    $out[] = "UPDATE `PREFIX_cms_lang` SET `meta_title` = ".$q($r['meta_title'])
        .", `meta_description` = ".$q($r['meta_description']).", `content` = ".$q($r['content'])
        ." WHERE `id_cms` = ".(int) $r['id_cms']." AND `id_lang` = ".(int) $r['id_lang'].";";
    $count++;
}
$out[] = "";

// ---- 9. Lời chứng thực giả ----
$out[] = "-- Ba lời chứng thực demo (Steve Rogers, Calrk Kent, John Doe) khen 'Hotel Prime'.";
foreach ($db->executeS('SELECT id_testimonial_block, active FROM `'._DB_PREFIX_.'htl_testimonials_block_data`') as $r) {
    $out[] = "UPDATE `PREFIX_htl_testimonials_block_data` SET `active` = ".(int) $r['active']
        ." WHERE `id_testimonial_block` = ".(int) $r['id_testimonial_block'].";";
    $count++;
}
$out[] = "";

// ---- 10. Sản phẩm demo ----
$out[] = "-- 10 sản phẩm demo QloApps. product.active KHÔNG đủ: cờ mà front office";
$out[] = "-- thực sự đọc nằm ở product_shop.active, nên phải đặt cả hai.";
foreach ($db->executeS('SELECT id_product, active, available_for_order, show_price, indexed FROM `'._DB_PREFIX_.'product` WHERE id_product <= 10') as $r) {
    $out[] = "UPDATE `PREFIX_product` SET `active` = ".(int) $r['active']
        .", `available_for_order` = ".(int) $r['available_for_order']
        .", `show_price` = ".(int) $r['show_price']
        .", `indexed` = ".(int) $r['indexed']
        ." WHERE `id_product` = ".(int) $r['id_product'].";";
    $out[] = "UPDATE `PREFIX_product_shop` SET `active` = ".(int) $r['active']
        ." WHERE `id_product` = ".(int) $r['id_product'].";";
    $count += 2;
}
$out[] = "";

// ---- 11. Khối phòng trang chủ ----
$out[] = "-- Khối phòng trang chủ: trỏ vào loại phòng thật, trải đủ 3 cơ sở.";
$out[] = "DELETE FROM `PREFIX_htl_room_block_data`;";
foreach ($db->executeS('SELECT id_product, active, position FROM `'._DB_PREFIX_.'htl_room_block_data` ORDER BY position') as $r) {
    $out[] = "INSERT INTO `PREFIX_htl_room_block_data` (`id_product`, `active`, `position`, `date_add`, `date_upd`)"
        ." VALUES (".(int) $r['id_product'].", ".(int) $r['active'].", ".(int) $r['position'].", NOW(), NOW());";
    $count++;
}

// Thay chỗ giữ chỗ bằng tiền tố bảng thật.
$sql = str_replace('PREFIX_', _DB_PREFIX_, implode("\n", $out))."\n";

$target = $root.'/docker/db/zz-content-fixes.sql';
if (file_put_contents($target, $sql) === false) {
    fwrite(STDERR, "LỖI: không ghi được {$target}. Kiểm tra quyền:\n"
        ."  docker compose exec web chown -R www-data:www-data /var/www/html/docker\n");
    exit(1);
}

echo "Đã ghi {$count} câu lệnh vào docker/db/zz-content-fixes.sql\n";
