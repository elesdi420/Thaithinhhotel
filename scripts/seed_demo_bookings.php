<?php
/**
 * Sinh dữ liệu đặt phòng mẫu cho buổi demo.
 *
 * Lịch phòng và dashboard đang trắng trơn vì bảng htl_booking_detail rỗng - hai
 * đơn có sẵn trong seed là đơn suông, không gắn phòng-đêm nào.
 *
 * Dữ liệu tạo ra là DỮ LIỆU MẪU, không phải khách thật: email đều thuộc miền
 * @demo.local để lọc và xoá về sau chỉ bằng một câu lệnh.
 *
 * Tự kiểm tra không để hai đơn trùng phòng trùng khoảng ngày, vì engine chống
 * overbooking sẽ mâu thuẫn với dữ liệu nếu để lọt.
 */

define('_PS_ADMIN_DIR_', '/var/www/html/admin2833tvua4');
require '/var/www/html/config/config.inc.php';

$db = Db::getInstance();
$today = new DateTime('today');

function demoDate($offsetDays)
{
    $x = new DateTime('today');
    if ($offsetDays >= 0) {
        $x->modify('+'.$offsetDays.' days');
    } else {
        $x->modify($offsetDays.' days');
    }
    return $x->format('Y-m-d');
}

// ---- Khách hàng mẫu -------------------------------------------------------
$guests = array(
    array('Nguyễn', 'Minh Anh',  'minh.anh@demo.local',   '0901000101'),
    array('Trần',   'Quốc Bảo',  'quoc.bao@demo.local',   '0901000102'),
    array('Lê',     'Thu Hà',    'thu.ha@demo.local',     '0901000103'),
    array('Phạm',   'Đức Duy',   'duc.duy@demo.local',    '0901000104'),
    array('Hoàng',  'Lan Chi',   'lan.chi@demo.local',    '0901000105'),
    array('Vũ',     'Thanh Tùng','thanh.tung@demo.local', '0901000106'),
);

$idCustomers = array();
foreach ($guests as $g) {
    $existing = (int) $db->getValue('SELECT id_customer FROM '._DB_PREFIX_."customer WHERE email = '".pSQL($g[2])."'");
    if ($existing) {
        $idCustomers[] = $existing;
        continue;
    }
    $c = new Customer();
    $c->firstname = $g[1];
    $c->lastname = $g[0];
    $c->email = $g[2];
    $c->passwd = Tools::encrypt('Password123!');
    $c->active = 1;
    $c->id_shop = 1;
    $c->id_shop_group = 1;
    $c->add();
    $idCustomers[] = (int) $c->id;
}
echo 'Khách mẫu: '.count($idCustomers)."\n";

$idAddress = (int) $db->getValue('SELECT id_address FROM '._DB_PREFIX_.'address WHERE deleted = 0 ORDER BY id_address');
$idCurrency = (int) Configuration::get('PS_CURRENCY_DEFAULT');

// ---- Phòng theo từng cơ sở ------------------------------------------------
$roomsByHotel = array();
$rows = $db->executeS(
    'SELECT ri.`id`, ri.`id_product`, ri.`id_hotel`, ri.`room_num`
     FROM '._DB_PREFIX_.'htl_room_information ri
     WHERE ri.`id_status` = 1 ORDER BY ri.`id_hotel`, ri.`id`'
);
foreach ($rows as $r) {
    $roomsByHotel[(int) $r['id_hotel']][] = $r;
}

$hotelNames = array();
foreach ($db->executeS(
    'SELECT hbi.`id`, cl.`name`
     FROM '._DB_PREFIX_.'htl_branch_info hbi
     INNER JOIN '._DB_PREFIX_.'category_lang cl ON cl.`id_category` = hbi.`id_category` AND cl.`id_lang` = 2'
) as $r) {
    $hotelNames[(int) $r['id']] = $r['name'];
}

/**
 * Kịch bản: cơ sở, số đêm, lệch ngày nhận so với hôm nay, trạng thái phòng,
 * trạng thái đơn.
 *   id_status phòng: 1 đã đặt chưa tới, 2 đang ở, 3 đã trả
 *   order state: 2 đã thanh toán đủ, 13 chờ thanh toán VietQR, 4 huỷ
 */
$plan = array(
    // Đã hoàn tất - tạo doanh thu quá khứ cho dashboard
    array(1, 2, -28, 3, 2), array(1, 3, -21, 3, 2), array(2, 2, -18, 3, 2),
    array(3, 1, -14, 3, 2), array(2, 4, -11, 3, 2), array(1, 2,  -7, 3, 2),
    array(3, 2,  -5, 3, 2),
    // Đang lưu trú - khách đang ở trong khách sạn hôm nay
    array(1, 3,  -1, 2, 2), array(2, 2,  -1, 2, 2), array(3, 3,  -2, 2, 2),
    array(1, 4,  -2, 2, 2),
    // Sắp tới - đã thanh toán
    array(2, 2,   2, 1, 2), array(1, 3,   4, 1, 2), array(3, 2,   6, 1, 2),
    array(2, 3,   9, 1, 2), array(1, 2,  13, 1, 2),
    // Sắp tới - chờ thanh toán VietQR
    array(3, 2,   3, 1, 13), array(2, 1,   8, 1, 13),

    // Đợt 2: dồn vào quanh hôm nay để công suất đêm demo trông thật.
    // 18 đơn rải đều 6 tuần chỉ cho 8% công suất - dashboard nhìn như ế.
    array(1, 3, -2, 2, 2), array(1, 2, -1, 2, 2), array(1, 4, -3, 2, 2),
    array(1, 2, -1, 2, 2), array(1, 3, -2, 2, 2), array(1, 2,  0, 2, 2),
    array(2, 3, -2, 2, 2), array(2, 2, -1, 2, 2), array(2, 4, -3, 2, 2),
    array(2, 2,  0, 2, 2), array(2, 3, -1, 2, 2),
    array(3, 2, -2, 2, 2), array(3, 3, -1, 2, 2), array(3, 2, -3, 2, 2),
    array(3, 4, -1, 2, 2), array(3, 2,  0, 2, 2),
    // Khách rời đi ngày mai/ngày kia -> có số liệu "sắp trả phòng"
    array(1, 1, -1, 2, 2), array(2, 1, -1, 2, 2),
);

$used = array();   // chống trùng phòng cùng khoảng ngày
$cursor = array(); // con trỏ chọn phòng cho từng cơ sở
$made = 0;
$stats = array('đã trả' => 0, 'đang ở' => 0, 'sắp tới' => 0, 'chờ thanh toán' => 0);

foreach ($plan as $i => $p) {
    list($idHotel, $nights, $offset, $roomStatus, $orderState) = $p;
    $from = demoDate($offset);
    $to   = demoDate($offset + $nights);

    // chọn phòng chưa bận trong khoảng này
    $pool = $roomsByHotel[$idHotel];
    $room = null;
    $n = count($pool);
    for ($k = 0; $k < $n; $k++) {
        $idx = (isset($cursor[$idHotel]) ? $cursor[$idHotel] : 0) % $n;
        $cursor[$idHotel] = $idx + 1;
        $cand = $pool[$idx];
        $clash = false;
        foreach ($used as $u) {
            if ($u['room'] == $cand['id'] && $from < $u['to'] && $to > $u['from']) {
                $clash = true;
                break;
            }
        }
        if (!$clash) {
            $room = $cand;
            break;
        }
    }
    if (!$room) {
        echo "Bỏ qua kịch bản $i: không còn phòng trống\n";
        continue;
    }
    $used[] = array('room' => $room['id'], 'from' => $from, 'to' => $to);

    $idProduct = (int) $room['id_product'];
    $unit = (float) Product::getPriceStatic($idProduct, true);
    $total = $unit * $nights;
    $idCustomer = $idCustomers[$i % count($idCustomers)];
    $paid = ((int) $orderState === 2);

    // Giỏ hàng
    $db->execute('INSERT INTO '._DB_PREFIX_.'cart
        (id_shop_group,id_shop,id_carrier,delivery_option,id_lang,id_address_delivery,id_address_invoice,id_currency,id_customer,id_guest,secure_key,recyclable,gift,gift_message,mobile_theme,allow_seperated_package,is_advance_payment,date_add,date_upd)
        VALUES (1,1,0,"",2,'.(int) $idAddress.','.(int) $idAddress.','.(int) $idCurrency.','.(int) $idCustomer.',1,"'.pSQL(md5(uniqid())).'",1,0,"",0,0,0,NOW(),NOW())');
    $idCart = (int) $db->Insert_ID();

    // Đơn hàng
    $reference = strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
    $db->execute('INSERT INTO '._DB_PREFIX_.'orders
        (reference,id_shop_group,id_shop,id_carrier,id_lang,id_customer,id_cart,id_currency,id_address_delivery,id_address_invoice,id_address_tax,current_state,secure_key,payment,payment_type,conversion_rate,module,recyclable,gift,gift_message,mobile_theme,total_discounts,total_discounts_tax_incl,total_discounts_tax_excl,total_paid,total_paid_tax_incl,total_paid_tax_excl,total_paid_real,total_products,total_products_wt,total_shipping,total_shipping_tax_incl,total_shipping_tax_excl,carrier_tax_rate,total_wrapping,total_wrapping_tax_incl,total_wrapping_tax_excl,round_mode,round_type,invoice_number,delivery_number,valid,invoice_date,date_add,date_upd)
        VALUES ("'.pSQL($reference).'",1,1,0,2,'.(int) $idCustomer.','.(int) $idCart.','.(int) $idCurrency.','.(int) $idAddress.','.(int) $idAddress.','.(int) $idAddress.','.(int) $orderState.',"'.pSQL(md5(uniqid())).'","Chuyển khoản VietQR",0,1,"qlovietqr",1,0,"",0,0,0,0,'.$total.','.$total.','.$total.','.($paid ? $total : 0).','.$total.','.$total.',0,0,0,0,0,0,0,0,0,0,0,'.($paid ? 1 : 0).',"'.pSQL($from).' 14:00:00","'.pSQL($from).' 14:00:00","'.pSQL($from).' 14:00:00")');
    $idOrder = (int) $db->Insert_ID();

    // Dòng sản phẩm
    $productName = (string) $db->getValue('SELECT name FROM '._DB_PREFIX_.'product_lang WHERE id_product = '.(int) $idProduct.' AND id_lang = 2');
    $db->execute('INSERT INTO '._DB_PREFIX_.'order_detail
        (id_order,id_order_invoice,id_warehouse,id_shop,product_id,product_attribute_id,product_name,product_quantity,product_quantity_in_stock,product_price,product_quantity_discount,total_price_tax_incl,total_price_tax_excl,unit_price_tax_incl,unit_price_tax_excl,original_product_price,is_booking_product)
        VALUES ('.(int) $idOrder.',0,0,1,'.(int) $idProduct.',0,"'.pSQL($productName).'",'.(int) $nights.','.(int) $nights.','.$unit.',0,'.$total.','.$total.','.$unit.','.$unit.','.$unit.',1)');
    $idOrderDetail = (int) $db->Insert_ID();

    // Lịch sử trạng thái
    $db->execute('INSERT INTO '._DB_PREFIX_.'order_history (id_employee,id_order,id_order_state,date_add)
        VALUES (0,'.(int) $idOrder.','.(int) $orderState.',"'.pSQL($from).' 14:05:00")');

    if ($paid) {
        $db->execute('INSERT INTO '._DB_PREFIX_.'order_payment
            (order_reference,id_currency,amount,payment_method,conversion_rate,transaction_id,date_add)
            VALUES ("'.pSQL($reference).'",'.(int) $idCurrency.','.$total.',"Chuyển khoản VietQR",1,"DEMO'.$idOrder.'","'.pSQL($from).' 14:05:00")');
    }

    // Phòng - đêm: chính là thứ làm lịch phòng có dữ liệu
    $checkIn = ($roomStatus >= 2) ? '"'.$from.' 14:00:00"' : '"0000-00-00 00:00:00"';
    $checkOut = ($roomStatus == 3) ? '"'.$to.' 12:00:00"' : '"0000-00-00 00:00:00"';
    $g = $guests[$i % count($guests)];

    $db->execute('INSERT INTO '._DB_PREFIX_.'htl_booking_detail
        (id_product,id_order,id_order_detail,id_cart,id_room,id_hotel,id_customer,booking_type,id_status,comment,check_in,check_out,date_from,date_to,total_price_tax_excl,total_price_tax_incl,total_paid_amount,is_back_order,hotel_name,room_type_name,city,state,country,zipcode,phone,email,room_num,adults,children,is_refunded)
        VALUES ('.(int) $idProduct.','.(int) $idOrder.','.(int) $idOrderDetail.','.(int) $idCart.','.(int) $room['id'].','.(int) $idHotel.','.(int) $idCustomer.',1,'.(int) $roomStatus.',"","'.trim($checkIn, '"').'","'.trim($checkOut, '"').'","'.pSQL($from).' 14:00:00","'.pSQL($to).' 12:00:00",'.$total.','.$total.','.($paid ? $total : 0).',0,"'.pSQL($hotelNames[$idHotel]).'","'.pSQL($productName).'","","","Việt Nam","","'.pSQL($g[3]).'","'.pSQL($g[2]).'","'.pSQL($room['room_num']).'",2,0,0)');

    ++$made;
    if ($orderState == 13) {
        ++$stats['chờ thanh toán'];
    } elseif ($roomStatus == 3) {
        ++$stats['đã trả'];
    } elseif ($roomStatus == 2) {
        ++$stats['đang ở'];
    } else {
        ++$stats['sắp tới'];
    }
}

echo "Đã tạo $made đơn đặt phòng mẫu\n";
foreach ($stats as $k => $v) {
    echo "  - $k: $v\n";
}
