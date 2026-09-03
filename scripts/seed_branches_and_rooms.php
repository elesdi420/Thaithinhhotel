<?php
/**
 * Script seeding 3 Hotel Branches and 50 Rooms for QloApps
 */

define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== START SEEDING: 3 BRANCHES & 50 ROOMS ===\n";

$languages = Language::getLanguages(true);
$defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');

// Clean up old demo rooms and room types to ensure exact 50 rooms
Db::getInstance()->execute("DELETE FROM " . _DB_PREFIX_ . "htl_room_information");
Db::getInstance()->execute("DELETE FROM " . _DB_PREFIX_ . "htl_room_type");

// Define the 3 branches
$branches = [
    1 => [
        'name' => 'Cơ sở 1 - Trung tâm',
        'address' => '123 Lê Lợi, Quận 1',
        'city' => 'Hồ Chí Minh',
        'phone' => '02838220001',
        'email' => 'branch1@hotel.local',
        'rating' => 4,
        'check_in' => '14:00',
        'check_out' => '12:00',
        'room_types' => [
            [
                'name' => 'Deluxe Room',
                'price' => 800000,
                'adults' => 2,
                'children' => 1,
                'floor' => 'Tầng 1',
                'start_num' => 101,
                'count' => 10
            ],
            [
                'name' => 'Suite Room',
                'price' => 1500000,
                'adults' => 4,
                'children' => 2,
                'floor' => 'Tầng 2',
                'start_num' => 111,
                'count' => 10
            ],
        ] // Total 20 rooms
    ],
    2 => [
        'name' => 'Cơ sở 2 - Bờ biển',
        'address' => '456 Võ Nguyên Giáp, Sơn Trà',
        'city' => 'Đà Nẵng',
        'phone' => '023638220002',
        'email' => 'branch2@hotel.local',
        'rating' => 4,
        'check_in' => '14:00',
        'check_out' => '12:00',
        'room_types' => [
            [
                'name' => 'Standard Room',
                'price' => 500000,
                'adults' => 2,
                'children' => 0,
                'floor' => 'Tầng 2',
                'start_num' => 201,
                'count' => 8
            ],
            [
                'name' => 'Deluxe Room',
                'price' => 800000,
                'adults' => 2,
                'children' => 1,
                'floor' => 'Tầng 3',
                'start_num' => 209,
                'count' => 7
            ],
        ] // Total 15 rooms
    ],
    3 => [
        'name' => 'Cơ sở 3 - Phố cổ',
        'address' => '789 Hàng Trống, Hoàn Kiếm',
        'city' => 'Hà Nội',
        'phone' => '02438220003',
        'email' => 'branch3@hotel.local',
        'rating' => 4,
        'check_in' => '14:00',
        'check_out' => '12:00',
        'room_types' => [
            [
                'name' => 'Standard Room',
                'price' => 500000,
                'adults' => 2,
                'children' => 0,
                'floor' => 'Tầng 3',
                'start_num' => 301,
                'count' => 8
            ],
            [
                'name' => 'Suite Room',
                'price' => 1500000,
                'adults' => 4,
                'children' => 2,
                'floor' => 'Tầng 4',
                'start_num' => 309,
                'count' => 7
            ],
        ] // Total 15 rooms
    ]
];

$totalRoomsSeeded = 0;
$shopId = (int) Context::getContext()->shop->id;

foreach ($branches as $branchId => $bData) {
    echo "\nProcessing Branch {$branchId}: {$bData['name']}...\n";

    // 1. Create or load category for hotel
    $hotel = new HotelBranchInformation($branchId);
    $category = null;
    if ($hotel->id && $hotel->id_category) {
        $category = new Category($hotel->id_category);
    }
    if (!$category || !Validate::isLoadedObject($category)) {
        $category = new Category();
        $category->id_parent = 6; // Demo City / Locations
        $category->active = 1;
    }
    foreach ($languages as $lang) {
        $category->name[$lang['id_lang']] = $bData['name'];
        $category->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($bData['name']);
    }
    $category->save();

    // 2. Create or update HotelBranchInformation
    $hotel->id_category = $category->id;
    $hotel->email = $bData['email'];
    $hotel->rating = $bData['rating'];
    $hotel->check_in = $bData['check_in'];
    $hotel->check_out = $bData['check_out'];
    $hotel->active = 1;
    foreach ($languages as $lang) {
        $hotel->hotel_name[$lang['id_lang']] = $bData['name'];
        $hotel->short_description[$lang['id_lang']] = $bData['name'] . ' - Dịch vụ tiêu chuẩn, vị trí thuận tiện.';
        $hotel->description[$lang['id_lang']] = '<p>' . $bData['name'] . ' mang lại trải nghiệm nghỉ dưỡng tiện nghi, hiện đại.</p>';
        $hotel->policies[$lang['id_lang']] = '<p>Check-in: ' . $bData['check_in'] . ' | Check-out: ' . $bData['check_out'] . '</p>';
    }
    $hotel->save();

    $newHotelId = $hotel->id;

    // 3. Create or update Address
    $idAddress = $hotel->getHotelIdAddress();
    $address = new Address($idAddress ? $idAddress : null);
    $address->id_hotel = $newHotelId;
    $address->id_country = 222; // Vietnam
    $address->alias = $bData['name'];
    $address->lastname = $bData['name'];
    $address->firstname = $bData['name'];
    $address->address1 = $bData['address'];
    $address->city = $bData['city'];
    $address->phone = $bData['phone'];
    $address->save();

    // 4. Create Room Types and Rooms
    foreach ($bData['room_types'] as $rType) {
        $prodName = $rType['name'] . ' (' . $bData['name'] . ')';

        $product = new Product();
        $product->id_shop_default = $shopId;
        $product->id_category_default = $category->id;
        $product->price = (float) $rType['price'];
        $product->active = 1;
        $product->quantity = 999999999;
        $product->booking_product = 1;
        $product->show_at_front = 1;
        $product->is_virtual = 1;
        $product->indexed = 1;
        foreach ($languages as $lang) {
            $product->name[$lang['id_lang']] = $prodName;
            $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($prodName);
            $product->description_short[$lang['id_lang']] = $rType['name'] . ' tại ' . $bData['name'];
            $product->description[$lang['id_lang']] = '<p>Phòng ' . $rType['name'] . ' với đầy đủ tiện nghi tiêu chuẩn tại ' . $bData['name'] . '.</p>';
        }
        $product->save();
        $product->addToCategories([$category->id]);
        StockAvailable::updateQuantity($product->id, null, 999999999);

        // Link in htl_room_type
        $roomType = new HotelRoomType();
        $roomType->id_product = $product->id;
        $roomType->id_hotel = $newHotelId;
        $roomType->adults = $rType['adults'];
        $roomType->children = $rType['children'];
        $roomType->max_adults = $rType['adults'];
        $roomType->max_children = $rType['children'];
        $roomType->max_guests = $rType['adults'] + $rType['children'];
        $roomType->min_los = 1;
        $roomType->save();

        // Seed individual rooms
        for ($i = 0; $i < $rType['count']; $i++) {
            $roomNum = (string) ($rType['start_num'] + $i);
            $room = new HotelRoomInformation();
            $room->id_product = $product->id;
            $room->id_hotel = $newHotelId;
            $room->room_num = $roomNum;
            $room->id_status = HotelRoomInformation::STATUS_ACTIVE;
            $room->floor = $rType['floor'];
            $room->comment = '';
            $room->save();
            $totalRoomsSeeded++;
        }
        echo "  - Added Room Type: {$rType['name']} ({$rType['count']} rooms from {$rType['start_num']})\n";
    }
}

Category::regenerateEntireNtree();

echo "\n============================================\n";
echo "SEEDING COMPLETED!\n";
echo "Total Branches Active: " . count($branches) . "\n";
echo "Total Rooms Seeded: {$totalRoomsSeeded}\n";
echo "============================================\n";
