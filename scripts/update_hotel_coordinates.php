<?php
define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== UPDATING HOTEL COORDINATES (STORY 3.1) ===\n";

$coords = [
    1 => ['lat' => 10.77250000, 'lng' => 106.69800000, 'city' => 'TP. Hồ Chí Minh'],
    2 => ['lat' => 16.06010000, 'lng' => 108.24350000, 'city' => 'Đà Nẵng'],
    3 => ['lat' => 21.03070000, 'lng' => 105.85040000, 'city' => 'Hà Nội']
];

foreach ($coords as $idHotel => $data) {
    Db::getInstance()->execute("
        UPDATE " . _DB_PREFIX_ . "htl_branch_info 
        SET latitude = " . (float) $data['lat'] . ", longitude = " . (float) $data['lng'] . " 
        WHERE id = " . (int) $idHotel
    );
    echo "Hotel {$idHotel} ({$data['city']}): Lat = {$data['lat']}, Lng = {$data['lng']}\n";
}

echo "\nCoordinates updated successfully!\n";
