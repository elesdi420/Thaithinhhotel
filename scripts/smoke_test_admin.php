<?php
/**
 * Chạy thử mọi trang trong khu quản trị và báo trang nào lỗi.
 *
 * Phân tích tĩnh chỉ bắt được mẫu lỗi đã biết. Script này dựng thật từng
 * controller rồi gọi trọn vòng đời run() - đúng luồng mà trình duyệt đi qua -
 * nên bắt được cả lỗi SQL, lỗi template lẫn lỗi PHP mà đọc mã không thấy.
 *
 * Đăng nhập được giả lập bằng cách nạp sẵn kết quả isLoggedBack vào Cache và
 * tự tính token CSRF bằng chính hàm của hệ thống (Tools::getAdminTokenLite),
 * nên không cần và không dùng mật khẩu của ai.
 *
 * Mỗi trang chạy trong MỘT TIẾN TRÌNH RIÊNG: nhiều controller gọi thẳng exit()
 * khi chuyển hướng, chạy chung một tiến trình thì trang đầu tiên làm vậy là
 * dừng cả vòng lặp. Tiến trình con báo kết quả về qua một dòng có tiền tố.
 *
 * Chạy: docker compose exec web php /var/www/html/scripts/smoke_test_admin.php [id_employee]
 */

$root = dirname(__DIR__);

// ---- Chế độ tiến trình con: chỉ chạy đúng một controller rồi in kết quả ----
if (isset($argv[1]) && strpos($argv[1], '--one=') === 0) {
    $class = substr($argv[1], 6);
    $idEmployee = isset($argv[2]) ? (int) $argv[2] : 1;

    define('_PS_ADMIN_DIR_', $root.'/admin2833tvua4');
    require_once $root.'/config/config.inc.php';
    // Entry point thật (admin/index.php) nạp thêm tệp này; thiếu nó thì các hàm
    // trợ giúp như getPath() không tồn tại và ta sẽ báo nhầm là lỗi của trang.
    require_once _PS_ADMIN_DIR_.'/functions.php';

    $_SERVER['HTTP_HOST'] = 'localhost:8080';
    $_SERVER['SERVER_NAME'] = 'localhost';
    $_SERVER['SCRIPT_NAME'] = '/admin2833tvua4/index.php';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    $employee = new Employee($idEmployee);
    Context::getContext()->employee = $employee;
    Context::getContext()->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
    Cache::store('isLoggedBack'.$employee->id, true);

    $ctrlClass = $class.'Controller';
    if (!class_exists($ctrlClass)) {
        fwrite(STDERR, "RESULT|SKIP|không nạp được lớp|0\n");
        exit;
    }

    $token = Tools::getAdminTokenLite($class);
    $_GET = array('controller' => $class, 'token' => $token);
    $_SERVER['REQUEST_URI'] = '/admin2833tvua4/index.php?controller='.$class.'&token='.$token;

    // exit() giữa chừng vẫn phải báo về được, nên chốt kết quả ở shutdown.
    $done = false;
    register_shutdown_function(function () use (&$done) {
        if ($done) {
            return;
        }
        $e = error_get_last();
        if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
            fwrite(STDERR, 'RESULT|FAIL|'.str_replace(array("\n", '|'), ' ', $e['message'])
                .' @ '.basename($e['file']).':'.$e['line']."|0\n");
        } else {
            // Thoát giữa chừng bằng exit/die - thường là chuyển hướng, không phải lỗi.
            fwrite(STDERR, "RESULT|EXIT|controller gọi exit (thường là chuyển hướng)|0\n");
        }
    });

    ob_start();
    try {
        $ctrl = new $ctrlClass();
        $ctrl->run();
        $out = ob_get_clean();
        $done = true;
        $kind = (strpos($out, 'Invalid security token') !== false) ? 'SKIP' : 'OK';
        $note = ($kind === 'SKIP') ? 'chặn bởi token' : '';
        fwrite(STDERR, 'RESULT|'.$kind.'|'.$note.'|'.strlen($out)."\n");
    } catch (\Throwable $ex) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        $done = true;
        fwrite(STDERR, 'RESULT|FAIL|'.get_class($ex).': '
            .str_replace(array("\n", '|'), ' ', $ex->getMessage())
            .' @ '.str_replace($root.'/', '', $ex->getFile()).':'.$ex->getLine()."|0\n");
    }
    exit;
}
// ---- Hết chế độ tiến trình con ----
define('_PS_ADMIN_DIR_', $root.'/admin2833tvua4');
require_once $root.'/config/config.inc.php';

$idEmployee = isset($argv[1]) ? (int) $argv[1] : 1;

$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/admin2833tvua4/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$employee = new Employee($idEmployee);
if (!Validate::isLoadedObject($employee)) {
    exit("Không có nhân viên id={$idEmployee}\n");
}
Context::getContext()->employee = $employee;
Context::getContext()->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
Cache::store('isLoggedBack'.$employee->id, true);

echo "Chạy thử với nhân viên: {$employee->email} (nhóm quyền {$employee->id_profile})\n";
echo str_repeat('=', 78)."\n";

/** Lấy mọi tab có class_name thật, theo thứ tự menu. */
$tabs = Db::getInstance()->executeS(
    'SELECT t.`class_name`, tl.`name`
     FROM `'._DB_PREFIX_.'tab` t
     LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl
       ON tl.`id_tab` = t.`id_tab` AND tl.`id_lang` = '.(int) Context::getContext()->language->id.'
     WHERE t.`class_name` != "" AND t.`active` = 1
     ORDER BY t.`id_parent`, t.`position`'
);

$ok = 0;
$skipped = array();
$failed = array();

foreach ($tabs as $tab) {
    $class = $tab['class_name'];
    $label = $tab['name'] ? $tab['name'] : $class;

    // Bỏ qua tab gom nhóm (không có controller thật) và tab của module.
    $file = $root.'/controllers/admin/'.$class.'Controller.php';
    if (!file_exists($file)) {
        $skipped[] = $class.' (không có controller lõi)';
        continue;
    }
    $ctrlClass = $class.'Controller';
    if (!class_exists($ctrlClass)) {
        $skipped[] = $class.' (không nạp được lớp)';
        continue;
    }

    // Gọi lại chính script này ở chế độ tiến trình con.
    $cmd = escapeshellarg(PHP_BINARY).' '.escapeshellarg(__FILE__)
        .' '.escapeshellarg('--one='.$class).' '.(int) $idEmployee.' 2>&1';
    $raw = shell_exec($cmd);

    $kind = 'FAIL';
    $note = 'không nhận được kết quả từ tiến trình con';
    $size = 0;
    if ($raw !== null && preg_match('/RESULT\|([A-Z]+)\|([^|]*)\|(\d+)/', $raw, $m)) {
        $kind = $m[1];
        $note = $m[2];
        $size = (int) $m[3];
    }

    if ($kind === 'OK') {
        $ok++;
        printf("  OK    %-32s %-26s %7d byte\n", $class, mb_substr($label, 0, 24), $size);
    } elseif ($kind === 'SKIP' || $kind === 'EXIT') {
        $skipped[] = $class.' ('.$note.')';
    } else {
        $failed[] = array('class' => $class, 'label' => $label, 'msg' => $note, 'at' => '');
        printf("  LỖI   %-32s %s\n", $class, mb_substr($label, 0, 24));
    }
}

echo str_repeat('=', 78)."\n";
echo 'Chạy được: '.$ok.'   |   Lỗi: '.count($failed).'   |   Bỏ qua: '.count($skipped)."\n\n";

if ($failed) {
    echo "CHI TIẾT CÁC TRANG LỖI\n".str_repeat('-', 78)."\n";
    foreach ($failed as $f) {
        echo "{$f['class']}  ({$f['label']})\n";
        echo "   {$f['msg']}\n\n";
    }
}
