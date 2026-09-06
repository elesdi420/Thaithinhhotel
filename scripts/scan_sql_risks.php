<?php
/**
 * Dò các mẫu SQL dễ vỡ trên MariaDB/MySQL đời mới trong khu quản trị.
 *
 * Ra đời sau sự cố trang Quản lý Đơn hàng trả HTTP 500: câu SELECT của danh
 * sách chứa một derived table (bảng con trong FROM) có GROUP BY riêng nhưng
 * bên trong lại tham chiếu ra alias của câu ngoài. Một khi derived table có
 * GROUP BY, engine buộc phải vật chất hoá nó độc lập trước, nên alias ngoài
 * không tồn tại ở thời điểm đó -> "Unknown column 'a.id_order' in 'WHERE'".
 * MariaDB không hỗ trợ LATERAL nên không có cách nào cứu cú pháp đó.
 *
 * Script chỉ ĐỌC và BÁO CÁO, không sửa gì. Nó cố tình báo cả trường hợp nghi
 * ngờ (ví dụ derived table không GROUP BY nhưng vẫn tham chiếu ra ngoài - loại
 * này thường được engine gộp phẳng nên chạy được) và đánh dấu mức độ, để người
 * đọc tự quyết. Không thay thế việc chạy thử thật.
 *
 * Chạy: docker compose exec web php /var/www/html/scripts/scan_sql_risks.php
 */

$root = dirname(__DIR__);

$dirs = array(
    $root.'/controllers/admin',
    $root.'/classes',
    $root.'/modules',
);

/** Alias hay dùng cho bảng chính trong HelperList của PrestaShop. */
$outerAliasPattern = '/\b(a|o|p|c|hbd|pl|cl)\.\s*`?[a-z_]+`?/i';

$findings = array();
$scanned = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        $path = $file->getPathname();
        if (strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        if (strpos($path, DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR) !== false) {
            continue;
        }
        $code = file_get_contents($path);
        if (stripos($code, 'SELECT') === false) {
            continue;
        }
        $scanned++;
        $rel = str_replace($root.DIRECTORY_SEPARATOR, '', $path);
        $lines = explode("\n", $code);

        // Tìm mọi "FROM (" - điểm mở đầu của một derived table.
        foreach ($lines as $i => $line) {
            if (!preg_match('/\bFROM\s*\(\s*SELECT/i', $line)) {
                continue;
            }
            // Gom khối từ dòng này tới khi cân bằng ngoặc, tối đa 25 dòng.
            $chunk = '';
            $depth = 0;
            $started = false;
            for ($j = $i; $j < min($i + 25, count($lines)); $j++) {
                $chunk .= $lines[$j]."\n";
                $opens = substr_count($lines[$j], '(');
                $closes = substr_count($lines[$j], ')');
                if (!$started && $opens > 0) {
                    $started = true;
                }
                $depth += $opens - $closes;
                if ($started && $depth <= 0) {
                    break;
                }
            }

            $hasGroupBy = (bool) preg_match('/\bGROUP\s+BY\b/i', $chunk);
            // Alias được khai báo NGAY TRONG khối này (an toàn nếu chỉ dùng cái này).
            preg_match_all('/\bFROM\s+`?[^`\s(]+`?\s+(?:AS\s+)?([a-z][a-z0-9_]{0,4})\b/i', $chunk, $decl);
            preg_match_all('/\bJOIN\s+`?[^`\s(]+`?\s+(?:AS\s+)?([a-z][a-z0-9_]{0,4})\b/i', $chunk, $declJoin);
            $declared = array_map('strtolower', array_merge($decl[1], $declJoin[1]));

            // Alias được THAM CHIẾU trong khối.
            preg_match_all('/\b([a-z][a-z0-9_]{0,4})\.\s*`?[a-z_]+`?/i', $chunk, $used);
            $usedAliases = array_unique(array_map('strtolower', $used[1]));

            $outside = array();
            foreach ($usedAliases as $al) {
                if (!in_array($al, $declared, true)) {
                    $outside[] = $al;
                }
            }
            if (!$outside) {
                continue;
            }

            $findings[] = array(
                'file' => $rel,
                'line' => $i + 1,
                'groupby' => $hasGroupBy,
                'outside' => $outside,
                'snippet' => trim(preg_replace('/\s+/', ' ', substr($chunk, 0, 150))),
            );
        }
    }
}

echo "Đã quét {$scanned} tệp PHP có chứa SELECT\n";
echo str_repeat('=', 78)."\n\n";

$critical = array_filter($findings, function ($f) { return $f['groupby']; });
$suspect  = array_filter($findings, function ($f) { return !$f['groupby']; });

echo "NGUY HIỂM - derived table CÓ GROUP BY và tham chiếu alias ngoài\n";
echo "(đây đúng là lỗi đã làm trang Đơn hàng trả 500)\n";
echo str_repeat('-', 78)."\n";
if (!$critical) {
    echo "Không còn trường hợp nào.\n";
}
foreach ($critical as $f) {
    echo "{$f['file']}:{$f['line']}\n";
    echo "   alias ngoài: ".implode(', ', $f['outside'])."\n";
    echo "   {$f['snippet']}\n\n";
}

echo "\nCẦN XEM LẠI - derived table KHÔNG có GROUP BY nhưng vẫn tham chiếu alias ngoài\n";
echo "(engine thường gộp phẳng được nên chạy bình thường; liệt kê để rà thủ công)\n";
echo str_repeat('-', 78)."\n";
if (!$suspect) {
    echo "Không có.\n";
}
foreach ($suspect as $f) {
    echo "{$f['file']}:{$f['line']}  (alias ngoài: ".implode(', ', $f['outside']).")\n";
}
