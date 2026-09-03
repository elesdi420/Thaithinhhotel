<?php
/**
 * Script seeding Roles, Hotel Scoping and Employees for QloApps
 */

define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== START SEEDING: ROLES, HOTEL SCOPING & EMPLOYEES ===\n";

$languages = Language::getLanguages(true);
$defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
$hasher = new PasswordHashing();
$commonPasswordHash = $hasher->passwordHash('Password123!');

$profilesDef = [
    'reception_1' => [
        'name' => 'Lễ tân - Cơ sở 1',
        'hotel_access' => [1 => 1, 2 => 0, 3 => 0],
        'is_reception' => true,
        'employee' => [
            'firstname' => 'Lễ tân',
            'lastname' => 'Cơ sở Một',
            'email' => 'reception1@hotel.local',
        ]
    ],
    'reception_2' => [
        'name' => 'Lễ tân - Cơ sở 2',
        'hotel_access' => [1 => 0, 2 => 1, 3 => 0],
        'is_reception' => true,
        'employee' => [
            'firstname' => 'Lễ tân',
            'lastname' => 'Cơ sở Hai',
            'email' => 'reception2@hotel.local',
        ]
    ],
    'reception_3' => [
        'name' => 'Lễ tân - Cơ sở 3',
        'hotel_access' => [1 => 0, 2 => 0, 3 => 1],
        'is_reception' => true,
        'employee' => [
            'firstname' => 'Lễ tân',
            'lastname' => 'Cơ sở Ba',
            'email' => 'reception3@hotel.local',
        ]
    ],
    'manager' => [
        'name' => 'Quản lý chuỗi',
        'hotel_access' => [1 => 1, 2 => 1, 3 => 1],
        'is_reception' => false,
        'employee' => [
            'firstname' => 'Quản lý',
            'lastname' => 'Toàn chuỗi',
            'email' => 'manager@hotel.local',
        ]
    ]
];

// Tabs allowed for receptionists:
// 9: AdminParentOrders, 22: AdminOrders, 10: AdminParentCustomer, 29: AdminCustomers,
// 84: AdminHotelReservationSystemManagement, 86: AdminHotelRoomsBooking
$receptionAllowedTabs = [9, 22, 10, 29, 84, 86];

foreach ($profilesDef as $key => $pData) {
    echo "\nProcessing Profile: {$pData['name']}...\n";

    // 1. Check if profile already exists by name
    $existingProfileId = (int) Db::getInstance()->getValue(
        "SELECT id_profile FROM `" . _DB_PREFIX_ . "profile_lang` WHERE `name` = '" . pSQL($pData['name']) . "'"
    );

    $profile = new Profile($existingProfileId ? $existingProfileId : null);
    foreach ($languages as $lang) {
        $profile->name[$lang['id_lang']] = $pData['name'];
    }
    $profile->save();
    $idProfile = (int) $profile->id;
    echo "  - Profile ID: {$idProfile}\n";

    // 2. Configure htl_access (Hotel Branch Scoping)
    foreach ($pData['hotel_access'] as $idHotel => $hasAccess) {
        Db::getInstance()->execute("
            REPLACE INTO `" . _DB_PREFIX_ . "htl_access` (`id_profile`, `id_hotel`, `access`)
            VALUES ({$idProfile}, {$idHotel}, {$hasAccess})
        ");
        echo "  - Hotel {$idHotel} access: " . ($hasAccess ? 'ALLOWED' : 'DENIED') . "\n";
    }

    // 3. Configure qlo_access (Tab Permissions)
    if ($pData['is_reception']) {
        // First reset all tabs to 0
        Db::getInstance()->execute("
            UPDATE `" . _DB_PREFIX_ . "access` 
            SET `view` = 0, `add` = 0, `edit` = 0, `delete` = 0 
            WHERE `id_profile` = {$idProfile}
        ");
        
        // Grant permissions for reception tabs
        foreach ($receptionAllowedTabs as $idTab) {
            Db::getInstance()->execute("
                UPDATE `" . _DB_PREFIX_ . "access` 
                SET `view` = 1, `add` = 1, `edit` = 1, `delete` = 0 
                WHERE `id_profile` = {$idProfile} AND `id_tab` = {$idTab}
            ");
        }
        // Explicitly ensure Stats and Dashboard are 0
        Db::getInstance()->execute("
            UPDATE `" . _DB_PREFIX_ . "access` 
            SET `view` = 0, `add` = 0, `edit` = 0, `delete` = 0 
            WHERE `id_profile` = {$idProfile} AND `id_tab` IN (1, 77, 73, 85)
        ");
    } else {
        // Manager gets view, add, edit across all tabs
        Db::getInstance()->execute("
            UPDATE `" . _DB_PREFIX_ . "access` 
            SET `view` = 1, `add` = 1, `edit` = 1, `delete` = 1 
            WHERE `id_profile` = {$idProfile}
        ");
    }

    // 4. Create or update Employee
    $empData = $pData['employee'];
    $existingEmpId = (int) Db::getInstance()->getValue(
        "SELECT id_employee FROM `" . _DB_PREFIX_ . "employee` WHERE email = '" . pSQL($empData['email']) . "'"
    );

    $employee = new Employee($existingEmpId ? $existingEmpId : null);
    $employee->firstname = $empData['firstname'];
    $employee->lastname = $empData['lastname'];
    $employee->email = $empData['email'];
    $employee->passwd = $commonPasswordHash;
    $employee->id_profile = $idProfile;
    $employee->id_lang = $defaultLangId;
    $employee->active = 1;
    $employee->default_tab = 86; // AdminHotelRoomsBooking (direct to calendar/rooms)
    $employee->save();
    echo "  - Employee created/updated: {$empData['email']} (Profile: {$idProfile})\n";
}

echo "\n============================================\n";
echo "ROLES AND EMPLOYEES SEEDING COMPLETED!\n";
echo "============================================\n";
