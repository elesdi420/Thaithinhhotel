<?php
define('_PS_MODE_DEV_', false);
require_once dirname(__DIR__) . '/config/config.inc.php';

echo "=== 1. ACTIVATING CCC PERFORMANCE SETTINGS (STORY 3.2) ===\n";

Configuration::updateValue('PS_SMARTY_CACHE', 1);
Configuration::updateValue('PS_SMARTY_FORCE_COMPILE', 0); // Recompile only if files modified
Configuration::updateValue('PS_CSS_THEME_CACHE', 1);     // Minify & combine CSS
Configuration::updateValue('PS_JS_THEME_CACHE', 1);      // Minify & combine JS
Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', 1); // Browser caching headers
Configuration::updateValue('PS_HTML_THEME_COMPRESSION', 1); // Minify HTML

echo "Smarty Cache: " . Configuration::get('PS_SMARTY_CACHE') . "\n";
echo "CSS CCC Cache: " . Configuration::get('PS_CSS_THEME_CACHE') . "\n";
echo "JS CCC Cache: " . Configuration::get('PS_JS_THEME_CACHE') . "\n";
echo "Browser Cache Control: " . Configuration::get('PS_HTACCESS_CACHE_CONTROL') . "\n";

echo "\n=== 2. POPULATING SEO META TITLES & DESCRIPTIONS ===\n";

$seoData = [
    'index' => [
        1 => [ // English
            'title' => 'CMS Hotel - Chain Hotel 3 Locations | Official Direct Booking',
            'desc' => 'Book direct at prime locations in Ho Chi Minh City, Da Nang, and Hanoi. Best rates guaranteed with automated VietQR payment.'
        ],
        2 => [ // Vietnamese
            'title' => 'CMS Hotel - Hệ thống Khách sạn Chuỗi 3 Cơ sở | Đặt phòng Trực tuyến',
            'desc' => 'Đặt phòng trực tiếp tại chuỗi khách sạn TP.HCM, Đà Nẵng, Hà Nội. Giá tốt nhất thị trường, hỗ trợ thanh toán mã QR VietQR 24/7.'
        ]
    ],
    'our-properties' => [
        1 => [
            'title' => 'Our Hotel Properties - CMS Hotel',
            'desc' => 'Explore CMS Hotel locations in Ho Chi Minh City, Da Nang, and Hanoi. Prime central locations with modern amenities.'
        ],
        2 => [
            'title' => 'Danh sách Chi nhánh Khách sạn - CMS Hotel',
            'desc' => 'Khám phá các chi nhánh khách sạn CMS Hotel tại TP. Hồ Chí Minh, Đà Nẵng và Hà Nội. Vị trí đắc địa, tiện nghi cao cấp.'
        ]
    ],
    'contact' => [
        1 => [
            'title' => 'Contact Us & Locations - CMS Hotel',
            'desc' => 'Contact details, hotlines, and Google Maps directions for CMS Hotel branches in Ho Chi Minh City, Da Nang, and Hanoi.'
        ],
        2 => [
            'title' => 'Liên hệ & Vị trí - CMS Hotel',
            'desc' => 'Thông tin liên hệ, hotline hỗ trợ 24/7 và bản đồ vị trí 3 chi nhánh CMS Hotel tại TP.HCM, Đà Nẵng, Hà Nội.'
        ]
    ]
];

foreach ($seoData as $page => $langs) {
    $idMeta = (int) Db::getInstance()->getValue("SELECT id_meta FROM " . _DB_PREFIX_ . "meta WHERE page = '" . pSQL($page) . "'");
    if ($idMeta) {
        foreach ($langs as $idLang => $meta) {
            Db::getInstance()->execute("
                REPLACE INTO " . _DB_PREFIX_ . "meta_lang (`id_meta`, `id_shop`, `id_lang`, `title`, `description`, `keywords`, `url_rewrite`)
                VALUES ({$idMeta}, 1, {$idLang}, '" . pSQL($meta['title']) . "', '" . pSQL($meta['desc']) . "', 'hotel, booking, vietqr, khach san', '" . pSQL($page) . "')
            ");
        }
        echo "Updated SEO Meta for page: [{$page}] (Meta ID: {$idMeta})\n";
    }
}

echo "\nSEO AND PERFORMANCE OPTIMIZATIONS COMPLETED!\n";
