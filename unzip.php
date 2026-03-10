<?php
/**
 * Auto Unzip Tool
 * Letakkan file ini di direktori yang sama dengan file .zip
 * Akses via browser: domain.com/unzip.php
 */

// ========================
// KONFIGURASI
// ========================
$config = [
    'password'        => '',        // Kosongkan jika tidak pakai password
    'delete_after'    => false,     // true = hapus .zip setelah diekstrak
    'overwrite'       => true,      // true = timpa file yang sudah ada
    'allowed_ext'     => ['zip'],   // ekstensi yang diizinkan
];

// ========================
// CEK PASSWORD (opsional)
// ========================
session_start();

if (!empty($config['password'])) {
    if (isset($_POST['logout'])) {
        unset($_SESSION['unzip_auth']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $config['password']) {
            $_SESSION['unzip_auth'] = true;
        } else {
            $loginError = 'Password salah!';
        }
    }
    if (empty($_SESSION['unzip_auth'])) {
        showLogin($loginError ?? null);
        exit;
    }
}

// ========================
// DIREKTORI KERJA
// ========================
$baseDir = __DIR__;
$messages = [];

// ========================
// PROSES UNZIP
// ========================
if (isset($_POST['action']) && $_POST['action'] === 'unzip') {
    $targetFile = isset($_POST['file']) ? basename($_POST['file']) : '';
    $filePath   = $baseDir . DIRECTORY_SEPARATOR . $targetFile;

    if ($targetFile && file_exists($filePath) && isAllowedFile($targetFile, $config['allowed_ext'])) {
        $result = extractZip($filePath, $baseDir, $config['overwrite']);
        if ($result['success']) {
            if ($config['delete_after']) {
                unlink($filePath);
                $result['message'] .= ' File ZIP telah dihapus.';
            }
            $messages[] = ['type' => 'success', 'text' => $result['message']];
        } else {
            $messages[] = ['type' => 'error', 'text' => $result['message']];
        }
    } else {
        $messages[] = ['type' => 'error', 'text' => 'File tidak ditemukan atau tidak diizinkan.'];
    }
}

// Unzip semua
if (isset($_POST['action']) && $_POST['action'] === 'unzip_all') {
    $zipFiles = getZipFiles($baseDir, $config['allowed_ext']);
    if (empty($zipFiles)) {
        $messages[] = ['type' => 'info', 'text' => 'Tidak ada file ZIP yang ditemukan.'];
    }
    foreach ($zipFiles as $zipFile) {
        $filePath = $baseDir . DIRECTORY_SEPARATOR . $zipFile;
        $result   = extractZip($filePath, $baseDir, $config['overwrite']);
        if ($result['success']) {
            if ($config['delete_after']) {
                unlink($filePath);
                $result['message'] .= ' (ZIP dihapus)';
            }
            $messages[] = ['type' => 'success', 'text' => "<strong>$zipFile</strong>: " . $result['message']];
        } else {
            $messages[] = ['type' => 'error', 'text' => "<strong>$zipFile</strong>: " . $result['message']];
        }
    }
}

// ========================
// FUNGSI
// ========================
function extractZip(string $filePath, string $destDir, bool $overwrite): array {
    if (!class_exists('ZipArchive')) {
        return ['success' => false, 'message' => 'ZipArchive tidak tersedia di server ini.'];
    }
    $zip = new ZipArchive();
    $res = $zip->open($filePath);
    if ($res !== true) {
        return ['success' => false, 'message' => "Gagal membuka ZIP. Kode error: $res"];
    }
    $count = $zip->numFiles;
    if ($overwrite) {
        $zip->extractTo($destDir);
    } else {
        for ($i = 0; $i < $count; $i++) {
            $name = $zip->getNameIndex($i);
            if (!file_exists($destDir . DIRECTORY_SEPARATOR . $name)) {
                $zip->extractTo($destDir, [$name]);
            }
        }
    }
    $zip->close();
    return ['success' => true, 'message' => "Berhasil diekstrak ($count file/folder)."];
}

function getZipFiles(string $dir, array $allowedExt): array {
    $files = scandir($dir);
    return array_values(array_filter($files, fn($f) => isAllowedFile($f, $allowedExt) && is_file($dir . DIRECTORY_SEPARATOR . $f)));
}

function isAllowedFile(string $filename, array $allowedExt): bool {
    return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), $allowedExt);
}

function formatBytes(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

function showLogin(?string $error): void {
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Unzip Tool</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 40px; width: 360px; }
  h2 { color: #f1f5f9; margin-bottom: 24px; font-size: 1.4rem; text-align: center; }
  input[type=password] { width: 100%; padding: 12px 14px; background: #0f172a; border: 1px solid #475569; border-radius: 8px; color: #f1f5f9; font-size: 0.95rem; margin-bottom: 14px; outline: none; }
  input[type=password]:focus { border-color: #6366f1; }
  button { width: 100%; padding: 12px; background: #6366f1; color: #fff; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
  button:hover { background: #4f46e5; }
  .error { color: #f87171; font-size: 0.85rem; margin-bottom: 12px; text-align: center; }
</style>
</head>
<body>
<div class="card">
  <h2>🔐 Unzip Tool</h2>
  <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="POST">
    <input type="password" name="password" placeholder="Masukkan password..." autofocus required>
    <button type="submit">Masuk</button>
  </form>
</div>
</body>
</html>
<?php
}

// ========================
// AMBIL DAFTAR FILE
// ========================
$zipFiles = getZipFiles($baseDir, $config['allowed_ext']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Unzip Tool</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #cbd5e1; min-height: 100vh; padding: 30px 16px; }
  .container { max-width: 780px; margin: 0 auto; }
  h1 { font-size: 1.6rem; color: #f1f5f9; margin-bottom: 6px; }
  .subtitle { color: #64748b; font-size: 0.9rem; margin-bottom: 28px; }
  .path-box { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; color: #94a3b8; margin-bottom: 24px; word-break: break-all; }
  .path-box span { color: #6366f1; font-weight: 600; }

  /* Messages */
  .msg { padding: 12px 16px; border-radius: 8px; margin-bottom: 10px; font-size: 0.9rem; }
  .msg.success { background: #052e16; border: 1px solid #16a34a; color: #4ade80; }
  .msg.error   { background: #1c0a0a; border: 1px solid #dc2626; color: #f87171; }
  .msg.info    { background: #0c1a2e; border: 1px solid #3b82f6; color: #93c5fd; }

  /* Table */
  .card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: #0f172a; }
  th { padding: 12px 16px; text-align: left; font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
  td { padding: 13px 16px; border-top: 1px solid #1e293b; font-size: 0.88rem; vertical-align: middle; }
  tr:hover td { background: #243044; }
  .fname { color: #e2e8f0; font-weight: 500; word-break: break-all; }
  .fsize { color: #64748b; white-space: nowrap; }
  .btn { padding: 7px 16px; border-radius: 7px; border: none; cursor: pointer; font-size: 0.83rem; font-weight: 600; transition: background .15s; }
  .btn-unzip { background: #6366f1; color: #fff; }
  .btn-unzip:hover { background: #4f46e5; }

  .empty { text-align: center; padding: 48px 20px; color: #475569; }
  .empty .icon { font-size: 3rem; margin-bottom: 12px; }

  /* Bottom bar */
  .bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: gap; gap: 10px; }
  .btn-all { padding: 10px 22px; background: #0ea5e9; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: 600; }
  .btn-all:hover { background: #0284c7; }
  .btn-out { padding: 10px 18px; background: transparent; color: #64748b; border: 1px solid #334155; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
  .btn-out:hover { color: #f87171; border-color: #f87171; }
  .info-note { font-size: 0.8rem; color: #475569; }
</style>
</head>
<body>
<div class="container">
  <h1>📦 Unzip Tool</h1>
  <p class="subtitle">Ekstrak file ZIP langsung dari browser</p>

  <div class="path-box">📁 Direktori: <span><?= htmlspecialchars($baseDir) ?></span></div>

  <?php foreach ($messages as $msg): ?>
    <div class="msg <?= $msg['type'] ?>"><?= $msg['text'] ?></div>
  <?php endforeach; ?>

  <div class="card">
    <?php if (empty($zipFiles)): ?>
      <div class="empty">
        <div class="icon">🗂️</div>
        <p>Tidak ada file ZIP di direktori ini.</p>
        <p style="font-size:0.82rem;margin-top:6px;">Upload file .zip ke direktori yang sama dengan unzip.php</p>
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nama File</th>
            <th>Ukuran</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($zipFiles as $i => $file): 
            $size = filesize($baseDir . DIRECTORY_SEPARATOR . $file);
          ?>
          <tr>
            <td style="color:#475569"><?= $i + 1 ?></td>
            <td class="fname">🗜️ <?= htmlspecialchars($file) ?></td>
            <td class="fsize"><?= formatBytes($size) ?></td>
            <td>
              <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="unzip">
                <input type="hidden" name="file" value="<?= htmlspecialchars($file) ?>">
                <button type="submit" class="btn btn-unzip">Ekstrak</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <div class="bar">
    <?php if (!empty($zipFiles)): ?>
    <form method="POST">
      <input type="hidden" name="action" value="unzip_all">
      <button type="submit" class="btn-all">⚡ Ekstrak Semua (<?= count($zipFiles) ?> file)</button>
    </form>
    <?php else: ?>
    <span></span>
    <?php endif; ?>

    <div style="display:flex;align-items:center;gap:12px">
      <span class="info-note">ZipArchive: <?= class_exists('ZipArchive') ? '✅ Aktif' : '❌ Tidak tersedia' ?></span>
      <?php if (!empty($config['password'])): ?>
      <form method="POST"><button type="submit" name="logout" class="btn-out">Logout</button></form>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>