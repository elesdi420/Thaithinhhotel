<?php
/**
 * Quét chuỗi tiếng Anh chưa dịch trong khu quản trị.
 *
 * Quét đệ quy:
 *   - controllers/admin/*.php và classes/controller/AdminController.php  ($this->l('...'))
 *   - classes/helper/*.php                                              ($this->l('...'))
 *   - admin<token>/themes/default/template/**\/*.tpl                     ({l s='...'})
 *
 * Đối chiếu với translations/vn/admin.php rồi in ra chuỗi CHƯA dịch, xếp theo
 * số lần xuất hiện giảm dần - dịch chuỗi ở đầu danh sách phủ được nhiều trang
 * nhất trên mỗi dòng từ điển bỏ ra.
 *
 * Chỉ đọc, không ghi gì. Dùng để soạn từ điển, không phải để sinh bản dịch.
 *
 * Chạy: docker compose exec web php /var/www/html/scripts/scan_admin_untranslated.php [số_dòng_in_ra]
 */

$root = dirname(__DIR__);
$limit = isset($argv[1]) ? (int) $argv[1] : 120;
/** Tham số 2: chỉ lấy file có đường dẫn chứa chuỗi này (vd "AdminProducts"). */
$filter = isset($argv[2]) ? $argv[2] : '';
/** Tham số 3: bỏ qua chuỗi dài hơn ngần này ký tự (câu thông báo dài ít khi cần). */
$maxLen = isset($argv[3]) ? (int) $argv[3] : 0;

/** Nạp từ điển đã có để biết chuỗi nào còn thiếu. */
$existing = array();
$adminPhp = $root.'/translations/vn/admin.php';
if (file_exists($adminPhp)) {
    require $adminPhp;
    $existing = isset($_LANGADM) ? $_LANGADM : array();
}

/** Thư mục quét. Bỏ qua cache và thư viện ngoài. */
$targets = array();
$adminDir = null;
foreach (glob($root.'/admin*', GLOB_ONLYDIR) as $d) {
    if (is_file($d.'/index.php') && is_dir($d.'/themes')) {
        $adminDir = $d;
    }
}
$targets[] = $root.'/controllers/admin';
$targets[] = $root.'/classes/controller';
$targets[] = $root.'/classes/helper';
if ($adminDir) {
    $targets[] = $adminDir.'/themes/default/template';
}

$counts = array();   // chuỗi => số lần gặp
$where  = array();   // chuỗi => vài file đầu tiên gặp nó

foreach ($targets as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        $path = $file->getPathname();
        if (strpos($path, DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR) !== false) {
            continue;
        }
        $ext = strtolower($file->getExtension());
        if ($ext !== 'php' && $ext !== 'tpl') {
            continue;
        }
        if ($filter !== '' && stripos($path, $filter) === false) {
            continue;
        }
        $code = file_get_contents($path);
        $found = array();

        if ($ext === 'php') {
            // $this->l('...') và $this->l("...")
            preg_match_all('/\$this->l\(\s*([\'"])((?:(?!\1|\\\\).|\\\\.)*)\1/', $code, $m, PREG_SET_ORDER);
            foreach ($m as $hit) {
                $found[] = stripcslashes($hit[2]);
            }
        } else {
            // {l s='...'} - bỏ qua chuỗi có mod= vì đó là dịch của module, không vào admin.php
            preg_match_all('/\{l\s+s=\s*([\'"])((?:(?!\1).)*)\1([^}]*)\}/s', $code, $m, PREG_SET_ORDER);
            foreach ($m as $hit) {
                if (stripos($hit[3], 'mod=') !== false) {
                    continue;
                }
                $found[] = stripcslashes($hit[2]);
            }
        }

        foreach ($found as $s) {
            $s = trim($s);
            // Bỏ chuỗi rỗng, chuỗi chỉ có ký hiệu, và chuỗi không chứa chữ Latin
            if ($s === '' || !preg_match('/[A-Za-z]{2,}/', $s)) {
                continue;
            }
            if ($maxLen > 0 && strlen($s) > $maxLen) {
                continue;
            }
            if (!isset($counts[$s])) {
                $counts[$s] = 0;
                $where[$s] = array();
            }
            $counts[$s]++;
            if (count($where[$s]) < 2) {
                $where[$s][] = str_replace($root.DIRECTORY_SEPARATOR, '', $path);
            }
        }
    }
}

/** Chuỗi đã có bản dịch trong bucket dùng chung thì loại ra. */
$missing = array();
foreach ($counts as $s => $n) {
    if (!isset($existing['AdminController'.md5($s)])) {
        $missing[$s] = $n;
    }
}
arsort($missing);

$totalStrings = count($counts);
$totalMissing = count($missing);
$covered = $totalStrings - $totalMissing;

echo "Tổng chuỗi dịch được tìm thấy : {$totalStrings}\n";
echo "Đã có bản dịch                : {$covered}\n";
echo "Còn thiếu                     : {$totalMissing}\n";
echo str_repeat('-', 78)."\n";
echo "TOP {$limit} chuỗi chưa dịch, xếp theo số lần xuất hiện:\n\n";

$i = 0;
foreach ($missing as $s => $n) {
    if ($i++ >= $limit) {
        break;
    }
    $short = strlen($s) > 68 ? substr($s, 0, 65).'...' : $s;
    printf("%3dx  %s\n", $n, $short);
}
