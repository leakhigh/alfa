<?php
$protocol   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$base_path  = $protocol . "://" . $_SERVER['HTTP_HOST'];

$domain_url = $base_path . '/shop/'; 
$base_url   = $base_path . '/shop/?video=';

$sitemap_name = 'sitemap'; 
$max_links_per_sitemap = 10000;
$local_file = 'kw.txt'; 


$raw_lines = file($local_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$keywords = array_map('trim', $raw_lines);
$keywords = array_filter($keywords);

if (empty($keywords)) {
    die("❌ No valid keywords found in {$local_file}");
}


$sitemap_index  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$sitemap_index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

$sitemap_files = [];

foreach ($keywords as $i => $keyword) {
    
    $clean_keyword = str_replace(' ', '+', $keyword);
    
    $encoded_keyword = $clean_keyword;

    $sitemap_num = ceil(($i + 1) / $max_links_per_sitemap);

    if (!isset($sitemap_files[$sitemap_num])) {
        $sitemap_files[$sitemap_num]  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap_files[$sitemap_num] .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    }

    $full_url = $base_url . $encoded_keyword;
    
    $escaped_url = htmlspecialchars($full_url, ENT_XML1, 'UTF-8');

    $sitemap_files[$sitemap_num] .= "  <url>" . PHP_EOL;
    $sitemap_files[$sitemap_num] .= "    <loc>{$escaped_url}</loc>" . PHP_EOL;
    $sitemap_files[$sitemap_num] .= "  </url>" . PHP_EOL;
}


foreach ($sitemap_files as $num => &$content) {
    $content .= '</urlset>' . PHP_EOL;
    $file_name = "{$sitemap_name}-{$num}.xml";
    file_put_contents($file_name, $content);

    $sitemap_index .= "  <sitemap>" . PHP_EOL;
    $sitemap_index .= "    <loc>" . htmlspecialchars($domain_url . $file_name, ENT_XML1, 'UTF-8') . "</loc>" . PHP_EOL;
    $sitemap_index .= "  </sitemap>" . PHP_EOL;
}


$sitemap_index .= '</sitemapindex>' . PHP_EOL;
file_put_contents("sitemap-index.xml", $sitemap_index);

echo "✅ Sitemap(s) created from local file '{$local_file}'.\n";
?>
